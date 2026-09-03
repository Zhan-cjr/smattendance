<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalLayer;
use App\Models\User;
use App\Models\Userkaryawan;
use Illuminate\Support\Facades\DB;

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
}
