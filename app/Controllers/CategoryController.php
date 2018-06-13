<?php
namespace App\Controllers;
use App\Models\User;
use App\Models\Event;
use App\Models\Cms;
use App\Models\Section;
use App\Models\CategoryPageSlider;
use App\Models\Eventgroup;
use App\Models\Category;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;


class CategoryController extends BaseController{

     
		public function getEventGroupsByCatID($request, $response){
			
					$arr_cat_data=array();
					$categoryid = $request->getAttribute('id'); //fetch the category id
					$dataEventGroups=array();
					$arrCategory  =  Category::where('id',$categoryid)->get()->first();; //section 1
					$advSliders = CategoryPageSlider::where('status','1')->get(); 	
					 
					 
					$event_list_section1=  Eventgroup::where('category_id',$categoryid)->get(); //section 1
					$event_list_section2=  Eventgroup::where('category_id',$categoryid)->get(); //section 2
					$event_list_section3=  Eventgroup::where('category_id',$categoryid)->get(); //section 2
				    $dataCatEventGroups[] =$arr_cat_data;
	 
				//End here//
			 /*End here */ 
					//Pagination start here
					$totalrecord=count($event_list_section1);
					 $page =$request->getParam('page');
                  $per_page = 8;
                  $page = (isset($page) && is_numeric($page) ) ? $page : 1;
                  $count = $totalrecord;
                  $start = ($page-1) * $per_page;
        

            $rows = Eventgroup::where('category_id',$categoryid)->limit($per_page)->offset($start)->get();
			
			     $this->data['rows'] = $rows;
				 $this->data['page'] = $page;
				 $this->data['pages'] = ceil($count / $per_page);
			 
				$dataArrSection1 = getEventGroupList($rows);  
				$dataArrSection2 = getEventGroupList($event_list_section2);
				$dataArrSection3 = getEventGroupList($event_list_section3);
				 
			  
				$this->data['catDetail'] = $arrCategory; 
				$this->data['active_category'] = $categoryid;
				$this->data['eventlistSection1'] = $dataArrSection1; 
				$this->data['eventlistSection2'] = $dataArrSection2;
				$this->data['eventlistSection3'] = $dataArrSection3; 
				
				$query = Cms::query();
				
				$this->data['catrightFirstAds'] =  htmlspecialchars_decode(getCmsList('1')); //First Right side Ads 
				$this->data['catrightSecondAds'] = htmlspecialchars_decode(getCmsList('2')); //First Right side Ads 
				$this->data['catrightThirdAds'] =  htmlspecialchars_decode(getCmsList('3')); //First Right side Ads 
				
				$this->data['advSliders'] =  $advSliders; //First Right side Ads 
				
				$this->data['egAdvSliderUrl'] =  CAT_PAGE_SLIDER_WEB_PATH.'/'; //First Right side Ads 
				  
				$this->data['egImgURL']  = EVENTGROUP_WEB_PATH.'/';
		   return $this->render($response, 'public/category/category_events.twig',$this->data);

		}
		
		 
}