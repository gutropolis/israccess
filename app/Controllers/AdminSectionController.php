<?php
namespace App\Controllers;

use App\Models;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;
/**
  Admin Section controller
  Available Functions.
  1. sections
  2. saveSection
  3. getSectionById
  4. updateSection
  5. deleteSectionById
  6. ajaxSectionsList
  
  
*/

class AdminSectionController extends Base 
{
	
	protected $container;
	protected $lang;
	// Class constructor
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	
	// Main function to display all sections
	public function sections($request, $response){
		$params = array( 'title' => $this->lang['section_all_txt'],
		                 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Section/sections.twig',$params);
	}
	
	// Save Section
	public function saveSection($request, $response){
	   $section_title = $request->getParam('section_title');
	   $section_name = $request->getParam('section_name');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $sectionExist = Models\Section::where('section_title', '=', $section_title)->first();
	   if($sectionExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Section (<strong>'.$section_title. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else if(empty($section_title)){
		 echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['section_title_msg_txt']));
		 exit();	   
	   }else if(empty($section_name)){
		 echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['section_name_msg_txt']));
		 exit();	   
	   }else{
		  
		   // Save to sections table
		   $section = new Models\Section;
		   $section->section_title = $section_title;
		   $section->section_name = $section_name;
		   $section->display_order = $display_order;
		   $section->status = $status;
		   $section->save();
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// get section by id
	public function getSectionById($request, $response, $args){
		$id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$section = Models\Section::find($id);
		if ($section) {
			return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode($section));
        }
	}
	
	// Update section
	public function updateSection($request, $response){
	   $id = $request->getParam('id');
	   $section_title = $request->getParam('section_title');
	   $section_name = $request->getParam('section_name');
	   $display_order = $request->getParam('display_order');
	   $status = $request->getParam('status');
	   $sectionExist = Models\Section::where('section_title', '=', $section_title)->where('id', '!=', $id)->first();
	   if($sectionExist){
		 echo json_encode(array("status" => 'duplicate', 
		                  'message' => 'Section (<strong>'.$section_title. '</strong>) '.$this->lang['common_already_exist_txt']));
		 exit();	   
	   }else if(empty($section_title)){
		 echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['section_title_msg_txt']));
		 exit();	   
	   }else if(empty($section_name)){
		 echo json_encode(array("status" => 'error', 
		                        'message' => $this->lang['section_name_msg_txt']));
		 exit();	   
	   }else{
		  
		   // Update sections table
		   $data = array('section_title' => $section_title,
		                 'section_name' => $section_name,
						 'display_order' => $display_order,
						 'status' => $status);
		 $section = Models\Section::where('id', '=', $id)->update($data);					 
		  return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	   }
	   
	   
	   
	}
	
	// Delete section by id
	public function deleteSectionById($request, $response, $args){
		 $id = $args['id'];
        $validations = [
            v::intVal()->validate($id)
        ];

        if ($this->validate($validations) === false) {
            return $response->withStatus(400);
        }
		$delete = Models\Section::find($id)->delete();
		return $response->withJson(json_encode(array("status" => TRUE)));
		
	}
	
	
	// Ajax sections list
	public function ajaxSectionsList($request, $response){
		
		$isSearched = $drpSearch = false; // set to false if user did not search something
		$conditions = array();
		$whereData =  $customSearch = $prefix = '';
		if($request->getParam('query') != null){
			 $isSearched = true; // user has searched something
			 $fields = array('section_title', 'section_name', 'status');
			if(isset($request->getParam('query')['Status'])) {
				   $drpSearch = true;
				   $customSearch  .= " status = ".$request->getParam('query')['Status']." ";
			 }
			 if(isset($request->getParam('query')['generalSearch'])) {
				 $prefix = '';
			 foreach($fields as $field){
			    if(isset($request->getParam('query')['generalSearch'])) {
				     $conditions[] = "$field LIKE '%" . ($request->getParam('query')['generalSearch']) . "%'";
				  }
			   }
			 }
		 }
		 $query = '';
		 if(count($conditions) > 0) {
            $query .=  '('. implode (' OR ', $conditions). ')'; 
         }
		if($drpSearch){
			 if($query <> ''){
				 $whereData =  $query. ' AND '. $customSearch;
			 }else{
				 $whereData = $customSearch;
			 }
		}else{
			$whereData = $query;
		}
        
		// Look for sorting if any 
		if($request->getParam('sort') != null){
			  $sort = $request->getParam('sort')['sort'];	
			  $field = $request->getParam('sort')['field'];	
		}else{
			$sort = 'desc';	
			  $field = 'id';	
			
		}
		
		
		
		
		$page     = $request->getParam('pagination')['page'];
		if( !empty($request->getParam('pagination')['pages']) ){
		  $pages    = $request->getParam('pagination')['pages'];
		}
		$per_page = $request->getParam('pagination')['perpage'];
		
		if($isSearched){
		    $total   = Models\Section::whereRaw($whereData)->count(); // get count 
		}else{
			$total   = Models\Section::get()->count(); // get count 
		}
		
		
		if($page == 1){
		   $offset = 0;	
		   $perpage = 0;
		}else{
		  $offset = ($page-1);	
		  $perpage = $per_page;	
		}
		if($per_page <= 1){
		  $pages = intval($total/10);
	    }else{
	      $pages = intval($total/$per_page);
		}
		if($per_page <= 1){
		   $perPageLimit = 10;	
		}else{
		   $perPageLimit = $per_page;	
		}
		if($isSearched){
		    $sections_list = Models\Section::whereRaw($whereData)->skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}else{
			$sections_list = Models\Section::skip($offset*$perPageLimit)->take($perPageLimit)->orderBy($field, $sort)->get();
		}
		
		$data = array();
		foreach($sections_list as $get){
		  	$array_data = array();
			$array_data['id']  = $get->id;
            $array_data['section_title']  = $get->section_title;
            $array_data['section_name']  = $get->section_name;
			$array_data['display_order']  = $get->display_order;
			$array_data['status']  = $get->status;
			$data[] = $array_data;
		}
		$meta = array("page" => $page,
						"pages" => $pages,
						"perpage" => $perPageLimit,
						"total" => $total,
						"sort" => $sort,
						"field" => $field
					);
		$output = array(
						"meta" => $meta,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
		
        
	}
	
	
	
	
}
