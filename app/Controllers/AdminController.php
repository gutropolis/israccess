<?php
namespace App\Controllers;
use Slim\Http\Request;
use App\Models\User;
use App\Models;
use App\Tools\Auth;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/*
*  Admin Controller
// Available Functions 

1.  settings
2.  save_settings
3.  edit_bulk_data
4.  updateSiteMode
5.  adminLanguage
*/
class AdminController extends Base 
{
	protected $container;
	protected $lang;
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
    
	//Settings page to display setting entries
	 public function settings($request, $response) {
		 $setting_data = Models\Setting::get()->first();
		 $params = array('title' => 'Settings',
		                 'setting_data' => $setting_data,
						 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Settings/settings.twig', $params);	
    }
	
	// Save setting entries
	public function save_settings($request, $response) {
		$site_phone = $request->getParam('site_phone');
		$facebook_link = $request->getParam('facebook_link');
		$instagram_link = $request->getParam('instagram_link');
		$twitter_link = $request->getParam('twitter_link');
		$btn_settings = $request->getParam('btn_settings');
		if( isset($btn_settings) ){
			// Check if data does exist update else save
			$checkData = Models\Setting::where('id','=',1)->get();
			if( !empty($checkData) ){
			   $data = array('site_phone' => $site_phone,
			              'facebook_link' => $facebook_link,
						  'twitter_link' => $twitter_link,
						  'instagram_link' => $instagram_link);
		        $settings = Models\Setting::where('id', '=', 1)->update($data);				  	
			}else{
				$setting = new Models\Setting;
				$setting->site_phone = $site_phone;
				$setting->facebook_link = $facebook_link;
				$setting->twitter_link = $twitter_link;
				$setting->instagram_link = $instagram_link;
				$setting->save();
			}
			
			return $response->withRedirect(base_url. '/admin/settings');
		}
        	
    }
	
	// Display selected option in popup
	public function display_selected_option_popup($request, $response)
    {
        $html = '';
		$selected_field = $request->getParam('selected_field');
		$table_name = $request->getParam('table_name');
        $html.='<form method="post" name="form_edit_bulk_data" id="form_edit_bulk_data"  autocomplete="Off" >';
		if($selected_field =='change_status')
		{
			$btn_class = 'primary';
			$btn_value = $this->lang['common_submit_txt'];
			$html.='<select name="new_value" class="form-control" required>';
				$html.='<option value="1">'.$this->lang['common_active_txt'].'</option>
					<option value="0">'.$this->lang['common_inactive_txt'].'</option>';
			$html.='</select>';
		}
		if($selected_field == 'delete_selected'){
			$btn_class = 'danger';
			$btn_value =  $this->lang['common_delete_txt'];
			$html.='<p>'.$this->lang['common_selected_rows_delete_txt'].'</p>';
			$html.='<input type="hidden" name="new_value" value="Delete">';
		}
        $html.='<input type="hidden" name="field_name" value="'.$selected_field.'">';
		$html.='<input type="hidden" name="table_name" value="'.$table_name.'">';
		
        foreach($_GET as $key => $value)
        {
            $html.='<input type="hidden" name="fields_ids[]" value="'.$value.'">';
        }
        $html.='<br><button class="btn btn-default pull-left" data-dismiss="modal" aria-hidden="true" >'.$this->lang['common_close_txt'].'</button><input type="button" onclick="return update_options_bulk_data()" class="btn btn-'.$btn_class.' SubmitBtn pull-right" value="'.$btn_value.'"  >
  </form>';
        echo $html;
    }
	
	// Edit bulk data base on request
	public function edit_bulk_data($request, $response){
		$new_value = $request->getParam('new_value');
		$field_name = $request->getParam('field_name');
		$table_name = $request->getParam('table_name');
		$fields_ids = $request->getParam('fields_ids');
		if( $request->getParam('fields_ids') != null)
        {
			foreach($request->getParam('fields_ids') as $id)
			{
				if($id == 'on')
				continue;
				// Check for the new value posted field
			   if($field_name == 'delete_selected'){
				   
				   // Check for table names
		   	       if($table_name == 'cities'){
						$delete = Models\City::find($id)->delete();
				   }
				   
				   // Delete from categories
				   if($table_name == 'categories'){
					   // Check if this category has a picture uploaded.
					   $pictureExist = Models\Category::where('id', '=', $id)->first()->picto_file;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(CATEGORY_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\Category::find($id)->delete();
				   }
				   
				   if($table_name == 'users'){
						$delete = Models\User::find($id)->delete();
						// Check if data is there for members or productors then delete them as well.
						$user_data = Models\Usermeta::where('user_id','=', $id)->get();
						if( !$user_data->isEmpty() ){
							$delete = Models\Usermeta::where('user_id','=', $id)->delete();
						}
						// Look for productors
						$pro_user_data = Models\Productor_meta::where('user_id','=', $id)->get();
						if( !$pro_user_data->isEmpty() ){
							$delete = Models\Productor_meta::where('user_id','=', $id)->delete();
						}
				   }
				   
				   if($table_name == 'auditoriums'){
					   $pictureExist = Models\Auditorium::where('id', '=', $id)->first()->background_file;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(AUDITORIUM_ROOT_PATH.'/thumbs/'.$pictureExist);
						   @unlink(AUDITORIUM_ROOT_PATH.'/'.$pictureExist);
					   }
						$delete = Models\Auditorium::find($id)->delete();
				   }
				   
				   if($table_name == 'events_group'){
					   $event_group_id = $id;
					    // First check in events table
						$events_list = Models\Event::where('eventgroup_id', '=', $event_group_id)->get();
						if( !empty($events_list) ){
						   foreach($events_list as $event):
						      $event_id = $event['id'];
						      // Now check if this event has some images uploaded
							  $events_images = Models\Eventpicture::where('event_id', '=', $event_id)->get();
							  if( !empty($events_images) ){
							    foreach($events_images as $img):
							       @unlink(EVENT_ROOT_PATH.'/thumbs/'.$img['event_img']);
								   @unlink(EVENT_ROOT_PATH.'/'.$img['event_img']);
							    endforeach;
								// Delete from event_images table
								$delete = Models\Eventpicture::where('event_id', '=', $event_id)->delete();
							  }
							   //Delete from events table
								$delete = Models\Event::find($event_id)->delete();
						   endforeach;	
						}
						
						// Now check if any uploaded picture in Event Group
						$pictureExist = Models\Eventgroup::where('id', '=', $event_group_id)->first()->group_picture;
					    if($pictureExist){
						   // Unlink the picture
						   @unlink(EVENTGROUP_ROOT_PATH.'/'.$pictureExist);
					     }
						 // Check if this event group has a picture uploaded.
						$group_thumbnail = Models\Eventgroup::where('id', '=', $id)->first()->group_thumbnail;
					    if($group_thumbnail){
						   // Unlink the picture
						   @unlink(EVENTGROUP_ROOT_PATH.'/thumbs/'.$group_thumbnail);
					     }
						 // Delete from the event group roles table
						 $delete = Models\EventGroupRole::where('eventgroup_id', '=', $event_group_id)->delete();
						 // Now delete from the main Event Group table
						$delete = Models\Eventgroup::find($event_group_id)->delete();
				   }
				   
				   
				   // Delete from sliders
				   if($table_name == 'sliders'){
					   // Check if this sliders has a picture uploaded.
						$pictureExist = Models\Slider::where('id', '=', $id)->first()->slider_picture;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(SLIDER_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\Slider::find($id)->delete();
				   }
				   // Delete from category_page_slider
				   if($table_name == 'category_page_slider'){
					   // Check if this sliders has a picture uploaded.
						$pictureExist = Models\CategoryPageSlider::where('id', '=', $id)->first()->slider_picture;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(CAT_PAGE_SLIDER_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\CategoryPageSlider::find($id)->delete();
				   }
				   
				    // Delete from advertisements
				   if($table_name == 'advertisements'){
					   // Check if this sliders has a picture uploaded.
						$pictureExist = Models\Advertisement::where('id', '=', $id)->first()->ad_picture;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(ADS_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\Advertisement::find($id)->delete();
				   }
				   
				   // Delete from payment types
				   if($table_name == 'payment_type'){
					   // Check if this payment has a picture uploaded.
						$pictureExist = Models\PaymentType::where('id', '=', $id)->first()->payment_logo;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(PAYMENT_TYPE_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\PaymentType::find($id)->delete();
				   }
				   
				   // Delete from partners
				   if($table_name == 'partners'){
					   // Check if this sliders has a picture uploaded.
						$pictureExist = Models\Partner::where('id', '=', $id)->first()->partner_logo;
					   if($pictureExist){
						   // Unlink the picture
						   @unlink(PARTNER_ROOT_PATH.'/'.$pictureExist);
					   }
					   $delete = Models\Partner::find($id)->delete();
				   }
				   
				   // Delete from communties
				   if($table_name == 'communities'){
						$communities = Models\Community::find($id)->delete();
				   }
				   
				   // Delete from currencies
		   	       if($table_name == 'currencies'){
						$delete = Models\Currency::find($id)->delete();
				   }
				   
				   // Delete from sections
		   	       if($table_name == 'sections'){
						$delete = Models\Section::find($id)->delete();
				   }
				   
				   
				   
		       }
			   
			   // Process all the change status stuff here
			   
			   if($field_name == 'change_status' ){
				   
				           // Check for cities
						   if($table_name == 'cities'){
							   $data = array('status' => $new_value);
							    $cities = Models\City::where('id', '=', $id)->update($data);
						   }
				           
						   // Check for categories
						   if($table_name == 'categories'){
							   $data = array('status' => $new_value);
							    $categories = Models\Category::where('id', '=', $id)->update($data);
						   }
				   
				           // Check for users
						   if($table_name == 'users'){
							   $data = array('status' => $new_value);
							    $users = Models\User::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for sliders
						   if($table_name == 'sliders'){
							   $data = array('status' => $new_value);
							    $sliders = Models\Slider::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for advertisements
						   if($table_name == 'advertisements'){
							   $data = array('status' => $new_value);
							    $sliders = Models\Advertisement::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for sliders
						   if($table_name == 'category_page_slider'){
							   $data = array('status' => $new_value);
							    $sliders = Models\CategoryPageSlider::where('id', '=', $id)->update($data);
						   }

						   // Check for payment types
						   if($table_name == 'payment_type'){
							   $data = array('status' => $new_value);
							    $payment_types = Models\PaymentType::where('id', '=', $id)->update($data);
						   }

						   // Check for partners
						   if($table_name == 'partners'){
							   $data = array('status' => $new_value);
							    $partners = Models\Partner::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for communties
						   if($table_name == 'communities'){
							   $data = array('status' => $new_value);
							    $communities = Models\Community::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for events
						   if($table_name == 'events'){
							   $data = array('status' => $new_value);
							    $events = Models\Event::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for currencies
						   if($table_name == 'currencies'){
							   $data = array('status' => $new_value);
							    $events = Models\Currency::where('id', '=', $id)->update($data);
						   }
						   
						   // Check for sections
						   if($table_name == 'sections'){
							    $data = array('status' => $new_value);
							    $events = Models\Section::where('id', '=', $id)->update($data);
						   }
				  
				   
			   }
			   
			} // End foreach
        }
		// response
		 return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE))); 
}
	
	// Change site mode
	public function updateSiteMode($request, $response){
		$is_for_maintenance = $request->getParam('is_for_maintenance');
		if($is_for_maintenance == 0){
			$new_value = '1';	
		}else{
		   $new_value = '0';	
		}
		$data = array('is_for_maintenance' => $new_value);
		$updated = Models\Setting::where('id', '=', 1)->update($data);
		return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE))); 
	}
	
	// Change language for Admin
	public function adminLanguage($request, $response, $args){
		$langId = $args['langId'];
		if( isset($langId) && !empty($langId) ){
			unset($_SESSION['default']);
			unset($_SESSION['adminLang']);	
			$_SESSION['defaultLang'] = $langId;
		}
		return $response
            ->withHeader('Content-type','application/json')
            ->write(json_encode(array('status' => TRUE)));
	}
	
	
	
	
}
