<?php

namespace App\Controllers\Admin\Settings;

use App\Controllers\AdminController;
use CodeIgniter\HTTP\ResponseInterface;
use InvalidArgumentException;
use RuntimeException;

class TrustLevelsController extends AdminController
{
    /**
     * Display the trust levels settings page.
     */
    public function index()
    {
        if ($this->request->is('post')) {
            return $this->update();
        }

        return $this->render('admin/settings/trust_levels', [
            'trustLevels'  => setting('TrustLevels.levels'),
            'trustActions' => setting('TrustLevels.actions'),
            'trustAllowed' => setting('TrustLevels.allowedActions'),
            'trustRequirements' => setting('TrustLevels.requirements'),
        ]);
    }

    /**
     * Update the trust levels settings.
     */
    private function update()
    {
        $this->validateData($this->request->getPost(), [
            'trust' => 'required',
            'requirements' => 'required',
        ]);

        $trust = $this->request->getPost('trust');
        $requirements = $this->request->getPost('requirements');

        $trustLevels = setting('TrustLevels.levels');
        $trustActions = setting('TrustLevels.actions');

        // Ensure the trust levels and actions are valid
        foreach ($trust as $level => $actions) {
            if (!isset($trustLevels[$level])) {
                throw new InvalidArgumentException('Invalid trust level: ' . $level);
            }

            foreach ($actions as $action => $value) {
                if (!isset($trustActions[$action])) {
                    throw new InvalidArgumentException('Invalid trust action: ' . $action);
                }
            }
        }

        // Ensure the requirements are valid
        foreach ($requirements as $level => $values) {
            if (!isset($trustLevels[$level])) {
                throw new InvalidArgumentException('Invalid trust level: ' . $level);
            }
        }

        // Ensure each trust level's allowed actions are converted to an array of the keys
        foreach ($trust as $level => $actions) {
            $trust[$level] = array_keys($actions);
        }

        setting('TrustLevels.allowedActions', $trust);
        setting('TrustLevels.requirements', $requirements);

        return redirect()->to(current_url())
            ->with('success', lang('Admin.settingsUpdated'));
    }
}
