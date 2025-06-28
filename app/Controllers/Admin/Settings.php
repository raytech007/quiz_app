<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    public function index()
    {
        $settings = $this->db->table('settings')->get()->getResultArray();

        // Convert to key-value array for easier access
        $settingsArray = [];
        foreach ($settings as $setting) {
            $settingsArray[$setting['setting_key']] = $setting['setting_value'];
        }

        $data = [
            'title' => 'System Settings',
            'active' => 'settings',
            'settings' => $settingsArray
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $settings = $this->request->getPost();

        $this->db->transStart();

        foreach ($settings as $key => $value) {
            if ($key === csrf_token()) continue; // Skip CSRF token

            $this->db->table('settings')
                ->where('setting_key', $key)
                ->update(['setting_value' => $value]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to update settings');
        }

        $this->logActivity(session()->get('id'), 'settings_update', 'Updated system settings');

        return redirect()->to('/admin/settings')->with('message', 'Settings updated successfully');
    }
}