<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Session;

//Includes WebClientPrint classes
include_once(app_path() . '/WebClientPrint/WebClientPrint.php');
use Neodynamic\SDK\Web\WebClientPrint;


class DemoPrintHomeController extends Controller
{
    public function index(){

        // $webClientPrintControllerAbsoluteURL = substr($currentAbsoluteURL, 0, strrpos($currentAbsoluteURL, '/')).'/WebClientPrintController.php';


        $webClientPrintControllerAbsoluteURL = action('WebClientPrintController@processRequest');

        $webClientPrintControllerAbsoluteURL = \URL::to('WebClientPrintController');
        

        $wcppScript = WebClientPrint::createWcppDetectionScript($webClientPrintControllerAbsoluteURL, Session::getId());    

        return view('DemoPrinthome.index', ['wcppScript' => $wcppScript]);
    }

    public function printersinfo(){

        $wcpScript = WebClientPrint::createScript(action('WebClientPrintController@processRequest'), action('HomeController@printersinfo'), Session::getId());    

        return view('DemoPrinthome.printersinfo', ['wcpScript' => $wcpScript]);

    }

    public function samples(){
        return view('DemoPrinthome.samples');
    }
    
}
