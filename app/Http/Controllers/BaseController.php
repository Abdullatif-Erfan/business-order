<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
// use Illuminate\Routing\Controller as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $role = '';
    protected $userId = '';
    protected $name = '';
    protected $roleText = '';
    protected $isAdmin = false;
    protected $accessInfo = [];
    protected $global = [];
    protected $lastLogin = '';
    protected $module = '';
    protected $khazanaAccountId = 1;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->initializeGlobalData();
    }

    /**
     * Check if the user is logged in.
     */
    protected function isLoggedIn()
    {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login page if not logged in
        }

        $user = Auth::user();
        $this->role = Session::get('roleText') ?? '';
        $this->userId = $user->id ?? '';
        $this->full_name = $user->full_name ?? '';
        $this->isAdmin = $user->isAdmin ?? false;
        $this->lastLogin = Session::get('lastLogin', now());
       
        $this->global = [
            'name' => $this->full_name,
            'role' => $this->role,
            'last_login' => $this->lastLogin,
            'is_admin' => $this->isAdmin,
            'kh_acc_id' => $this->khazanaAccountId,
        ];
    }

    /**
     * Check if the user is an admin.
     */
    protected function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Check if the user has list access.
     */
    protected function hasListAccess()
    {
        // Check if the user is an admin
        if ($this->isAdmin()) { return true;  }
    
        // Check if the module exists in accessInfo and validate 'list' or 'total_access' permissions
        if (
            isset($this->accessInfo[$this->module]) &&
            (
                $this->accessInfo[$this->module]['list'] == 1 ||
                $this->accessInfo[$this->module]['total_access'] == 1
            )
        ) {
            return true;
        }
    
        // Default to no access
        return false;
    }


    /**
     * Initialize global data (example).
     */
    private function initializeGlobalData()
    {
        $this->global['app_name'] = config('app.name');
        $this->global['version'] = '1.0.0'; // Example global data
    }

     /**
	 * This function is used to check the user having create access or not
	 */
    protected function hasCreateAccess()
    {
        if ($this->isAdmin() ||
            (isset($this->accessInfo[$this->module]) &&
            (
                $this->accessInfo[$this->module]['create_records'] == 1 ||
                $this->accessInfo[$this->module]['total_access'] == 1
            ))) {
            return true;
        }
        return false;
    }

    /**
	 * This function is used to check the user having update access or not
	 */
    protected function hasUpdateAccess()
    {
        if ($this->isAdmin() ||
            (isset($this->accessInfo[$this->module]) &&
            (
                $this->accessInfo[$this->module]['edit_records'] == 1 ||
                $this->accessInfo[$this->module]['total_access'] == 1
            ))) {
            return true;
        }
        return false;
    }

	/**
	 * This function is used to check the user having delete access or not
	 */
    protected function hasDeleteAccess()
    {
        if ($this->isAdmin() ||
            (isset($this->accessInfo[$this->module]) &&
            (
                $this->accessInfo[$this->module]['delete_records'] == 1 ||
                $this->accessInfo[$this->module]['total_access'] == 1
            ))) {
            return true;
        }
        return false;
    }


    /**
	 * This function is used to load the set of views
	 */
    public function loadThis()
    {
        return view('component.header')
            ->with('component.sidebar', $this->global)
            ->with('component.access', true)
            ->with('component.footer', true);
    }


    /**
	 * This function is used to logged out user from system
	 */
    public function logout()
    {
        Auth::logout(); // Use Laravel's built-in Auth facade for logout
        return redirect()->route('login'); // Redirect to login page
    }

    /**
	 * This function is used to load dashboard view in the home controller
	 * @param {mixed}	$sidebarInfo:  This is array of sidebar information
	 * @param {mixed}	$pageInfo:	   This is array of page information
	 * @param {mixed}	$footerInfo:   This is array of footer information
	 */

    public function loadDashboardViews($sidebarInfo = null, $pageInfo = null, $footerInfo = null)
    {
        return view('component.header')
            ->with('sidebar', $sidebarInfo)
            ->with('footer', $footerInfo)
            ->with('dashboard', $pageInfo);
    }

    /**
     * Load a view with a common structure.
     */
    public function loadViews($viewName = "", $headerInfo = null, $pageInfo = null, $footerInfo = null)
    {
        return view('component.header')
            ->with('sidebar', $headerInfo)
            ->with('content', $viewName)
            ->with('page', $pageInfo)
            ->with('footer', $footerInfo);
    }
}
