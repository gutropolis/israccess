<?php
namespace App\Controllers;

use App\Models\User;
use App\Models;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin CMS Controller
*  CRUDs for CMS
   Available Functions
   1. cms
   2. getCmsById
   3. saveCms
   4. updateCms
   5. deleteCmsById
*/
class AdminCmsController extends Base 
{
	protected $container;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
	}
	// Main function to display all CMS pages in admin
	public function cms($request, $response) {
		$cms_list = Models\Cms::get();
        $params = array( 'title' => 'All Cms Pages',
						  'cms_list' => $cms_list,
						  'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Cms/cms.twig',$params);
    }
	
	
	
	
	// Get Cms by id
	public function getCmsById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$cms_data = Models\Cms::find($id);
		$cms_data['description'] = htmlspecialchars_decode($cms_data['description']);
		$params = array( 'title' => 'Cms Page',
						 'cms_data' => $cms_data);
        return $this->render($response, ADMIN_VIEW.'/Cms/edit.twig',$params);
		
	}
	
	// Save CMS from Admin
	public function saveCms($request, $response){
	   $isError = false;
	   $title = $request->getParam('title');
	   $description = $request->getParam('description');
	   $titleExist = Models\Cms::where('title', '=', $title)->first();
	   if( $titleExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Title (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter cms title'));
		 exit();	   
	   }else  if( empty($description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter cms description'));
		 exit();	   
	   } else{
		   // Save to cities table
		   $cms = new Models\Cms;
		   $cms->title = $title;
		   $cms->description = htmlspecialchars($description);
		   $cms->save();
		   
		   return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));  
	   }
	   
	   
	   
	   
	}
	
	// Update Cms from Admin
	public function updateCms($request, $response){
		
	   $id   = $request->getParam('id');
	   $title = $request->getParam('title');
	   $description = $request->getParam('description');
	   $titleExist = Models\Cms::where('title', '=', $title)->where('id', '!=', $id)->first();
	   if( $titleExist){
		  
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Title (<strong>'.$title. '</strong>) already exist.'));
		 exit();	   
	   }else  if( empty($title) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter cms title'));
		 exit();	   
	   }else  if( empty($description) ){
		  
		 echo json_encode(array("status" => 'error', 
		                  'message' => 'Please enter cms description'));
		 exit();	   
	   } else{
		   
		   // Save to category table
		   $data = array('title' => $title,
		                 'description' => htmlspecialchars($description)
						 );
		  $cms = Models\Cms::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	}
	
	// Delete Cms From Admin
	public function deleteCmsById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		
		$delete = Models\Cms::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	
	
	
	
	
	
	
}
