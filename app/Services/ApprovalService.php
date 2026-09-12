<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalLayer;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalService
{
    /**
     * Get the specific layer for a feature, level, and context (Cabang, Dept, Jabatan).
     * Implements override logic: Cabang > Dept > Jabatan > Global
     */
    public function getLayer($feature, $level, $kodeDept = null, $kodeJabatan = null, $kodeCabang = null)
    {
        // Fetch all candidate layers for this feature and level
        $layers = ApprovalLayer::where('feature', $feature)
            ->where('level', $level)
            ->get();

        $validLayers = $layers->filter(function ($layer) use ($kodeCabang, $kodeDept, $kodeJabatan) {
            $cabangMatch = is_null($layer->kode_cabang) || $layer->kode_cabang === $kodeCabang;
            $deptMatch = is_null($layer->kode_dept) || $layer->kode_dept === $kodeDept;
            $jabatanMatch = is_null($layer->kode_jabatan) || $layer->kode_jabatan === $kodeJabatan;
            return $cabangMatch && $deptMatch && $jabatanMatch;
        });

        if ($validLayers->isEmpty()) {
            return null;
        }

        // Sort by specificity: Cabang (100) > Dept (10) > Jabatan (1)
        $bestLayer = $validLayers->sortByDesc(function ($layer) {
            $score = 0;
            if (!is_null($layer->kode_cabang)) $score += 100;
            if (!is_null($layer->kode_dept)) $score += 10;
            if (!is_null($layer->kode_jabatan)) $score += 1;
            return $score;
        })->first();

        return $bestLayer;
    }

    /**
     * Check if a user can approve the current step.
     * Supports delegation: karyawan with linked approval admin can approve using admin's role.
     *
     * @param string $feature
     * @param int $currentLevel
     * @param string $userRole
     * @param string|null $kodeDept
     * @param string|null $kodeJabatan
     * @param User|null $user The authenticated user (needed for delegation check)
     * @param string|null $kodeCabang
     * @return bool
     */
    public function canApprove($feature, $currentLevel, $userRole, $kodeDept = null, $kodeJabatan = null, $user = null, $kodeCabang = null)
    {
        // Get the rule that applies to this context for the current level
        $rule = $this->getLayer($feature, $currentLevel, $kodeDept, $kodeJabatan, $kodeCabang);

        if (!$rule) {
            return false;
        }

        // Direct role match
        if ($userRole === $rule->role_name) {
            return true;
        }

        // Check via linked approval admin (delegation)
        if ($user && $userRole === 'karyawan') {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan && $userkaryawan->approval_admin_id) {
                $admin = User::find($userkaryawan->approval_admin_id);
                if ($admin) {
                    $adminRole = $admin->getRoleNames()->first();
                    return $adminRole === $rule->role_name;
                }
            }
        }

        return false;
    }

    /**
     * Get the approval admin ID for delegation.
     * If user is karyawan with linked admin, return admin's ID.
     * Otherwise return the user's own ID.
     *
     * @param User $user
     * @return int
     */
    public function getApprovalUserId($user)
    {
        if ($user->hasRole('karyawan')) {
            $userkaryawan = Userkaryawan::where('id_user', $user->id)->first();
            if ($userkaryawan && $userkaryawan->approval_admin_id) {
                return $userkaryawan->approval_admin_id;
            }
        }
        return $user->id;
    }

    /**
     * Get all User IDs who are authorized to approve for a given feature, level, and context.
     *
     * @param string $feature
     * @param int $level
     * @param string|null $kodeDept
     * @param string|null $kodeJabatan
     * @param string|null $kodeCabang
     * @return array<int>
     */
    public function getApproverUserIds(string $feature, int $level, ?string $kodeDept = null, ?string $kodeJabatan = null, ?string $kodeCabang = null): array
    {
        $layer = $this->getLayer($feature, $level, $kodeDept, $kodeJabatan, $kodeCabang);
        $approverUserIds = [];

        if ($layer && !empty($layer->role_name)) {
            $roleName = $layer->role_name;

            // 1. Direct users with this role
            $directUsers = User::role($roleName)->get();
            foreach ($directUsers as $u) {
                if ($this->userMatchesContext($u, $kodeCabang, $kodeDept)) {
                    $approverUserIds[] = $u->id;
                }
            }

            // 2. Delegated karyawan users whose linked admin has this role
            $adminIds = $directUsers->pluck('id')->toArray();
            if (!empty($adminIds)) {
                $delegatedKaryawanUsers = Userkaryawan::whereIn('approval_admin_id', $adminIds)
                    ->pluck('id_user')
                    ->toArray();

                foreach ($delegatedKaryawanUsers as $karyawanUserId) {
                    $u = User::find($karyawanUserId);
                    if ($u) {
                        $admin = $u->getApprovalAdmin();
                        if ($admin && $this->userMatchesContext($admin, $kodeCabang, $kodeDept)) {
                            $approverUserIds[] = $u->id;
                        }
                    }
                }
            }
        } else {
            // Fallback: Super Admin + users who have approval role/permission for this context
            $superAdmins = User::role('super admin')->get();
            foreach ($superAdmins as $sa) {
                $approverUserIds[] = $sa->id;
            }

            // Admin / HRD / administrator with matching branch & dept
            $otherAdmins = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'hrd', 'administrator']);
            })->get();

            foreach ($otherAdmins as $oa) {
                if ($this->userMatchesContext($oa, $kodeCabang, $kodeDept)) {
                    $approverUserIds[] = $oa->id;
                }
            }
        }

        return array_values(array_unique($approverUserIds));
    }

    /**
     * Check if a user's branch and department access matches the target context.
     */
    protected function userMatchesContext(User $user, ?string $kodeCabang, ?string $kodeDept): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $userCabangs = $user->getCabangCodes();
        if (!empty($userCabangs) && $kodeCabang !== null && !in_array($kodeCabang, $userCabangs)) {
            return false;
        }

        $userDepts = $user->getDepartemenCodes();
        if (!empty($userDepts) && $kodeDept !== null && !in_array($kodeDept, $userDepts)) {
            return false;
        }

        return true;
    }

    /**
     * Send push notification to all approvers for a new or escalated permission request.
     *
     * @param \App\Models\Karyawan|object $karyawan
     * @param string $typeLabel (e.g. 'Izin Absen', 'Cuti', 'Izin Sakit', 'Izin Dinas')
     * @param string $detailDates (e.g. '12 Sep 2026 s.d 14 Sep 2026')
     * @param string $url
     * @param int $level
     * @param string $feature
     */
    public function notifyApprovers($karyawan, string $typeLabel, string $detailDates, string $url, int $level = 1, string $feature = 'IZIN'): void
    {
        try {
            $approverIds = $this->getApproverUserIds(
                $feature,
                $level,
                $karyawan->kode_dept ?? null,
                $karyawan->kode_jabatan ?? null,
                $karyawan->kode_cabang ?? null
            );

            if (empty($approverIds)) {
                return;
            }

            $nama = $karyawan->nama_karyawan ?? 'Karyawan';
            $levelText = $level > 1 ? " (Tahap {$level})" : "";
            $title = "📋 Pengajuan {$typeLabel} Baru{$levelText}";
            $body = "{$nama} mengajukan {$typeLabel} ({$detailDates}). Menunggu persetujuan Anda.";

            app(WebPushService::class)->sendToUsers($approverIds, $title, $body, $url);
        } catch (\Exception $e) {
            Log::warning("ApprovalService notifyApprovers error: " . $e->getMessage());
        }
    }
}
