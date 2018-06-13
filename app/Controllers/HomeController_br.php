<?php
namespace App\Controllers;
use App\Models\User;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

class HomeController extends Base{

    public function getHome($request, $response){
	    return $this->render($response, HOME_VIEW.'/home.twig', []);
    }
	
	public function comingSoon(RequestInterface $request, ResponseInterface $response){
		return $this->response->withStatus(200)->withHeader('Location', base_url.'/coming-soon.php'); 
    }
	
	
	public function getBooking($request, $response){ 	 
	   return $this->render($response, HOME_VIEW.'/booking.twig'); 
	}
	public function checkOrder($request, $response){  
	  return $this->render($response, HOME_VIEW.'/order.twig'); 
	}
	public function getItemBySearch($request, $response){ 	 
	   return $this->render($response, HOME_VIEW.'/mysearch.twig'); 
	}
	public function upcomingEvent($request, $response){ 	 
	   return $this->render($response, HOME_VIEW.'/do-no-miss.twig'); 
	}
	public function eventDay($request, $response){
		 return $this->render($response, HOME_VIEW.'/events-of-day.twig'); 
    }
	
	/* ===========Add code on 22 April =============*/
	
	 public function checkMyOrder($request, $response){

        return $this->render($response, HOME_VIEW.'/myorder.twig');

    }
    public function getCommunity($request, $response){

        return $this->render($response, HOME_VIEW.'/culturaccess-community.twig');

    }
   

    public function getTheaterComedyzero($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy-00.twig');

    }
    public function getTheaterComedyone($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy-01.twig');

    }
	
	
	//Add categories controller //
	
	 public function getTheaterComedy($request, $response){

        return $this->render($response, HOME_VIEW.'/theater-comedy.twig');

    }
	
	 public function getConcerts($request, $response){

        return $this->render($response, HOME_VIEW.'/concerts-musique.twig');

    }
	 public function getOperaDense($request, $response){

        return $this->render($response, HOME_VIEW.'/opera-danse.twig');

    }
	 public function getCultureExpo($request, $response){

        return $this->render($response, HOME_VIEW.'/culture-expos.twig');

    }
	 public function getSports($request, $response){

        return $this->render($response, HOME_VIEW.'/sports-loisirs.twig');

    }
	 public function getTourismVisitGuide($request, $response){

        return $this->render($response, HOME_VIEW.'/tourisme-visite-guide.twig');

    }
	
	
	/*========End here  ============================*/
	
	
	
	 public function GetAllCategories($request, $response)
    {
        $mycateg = Categories::all();
        echo $mycateg;
    }

	public function getAllUsers($request, $response)
	{
		
		return $this->render($response, 'admin/partial/user/index.twig');
    }

	public function getUsersById($request, $response)
	{
		$id=$request->getAttribute('id');
		$userid=User::where('id', $id)->first();
		return $this->render($response,'admin/partial/user/edit.twig',['mytable' => $userid]);
    }

	public function GetAllAuditoriums($request, $response)
	{
		$auditorium = Auditorium::all();

		echo $auditorium;
    }
	public function GetAllArtist($request, $response)
	{
		$artist = Artist::all();

		echo $artist;
    }
	public function getAllEvents_Group($request, $response)
	{
		$eventg = Events_Group::all();

		echo $eventg;
    }

	public function getAllEvents($request, $response)
	{
		
		return $this->_view->render($response, 'admin/partial/event/index.twig');
    }

	public function getEventById($request, $response)
	{
		$id=$request->getAttribute('id');
		$userid=User::where('id', $id)->first();
		return $this->_view->render($response,'admin/partial/event/edit.twig',['mytable' => $userid]);
    }


	public function getPost($request, $response)
	{

		return $this->_view->render($response,'admin/partial/post/create_post.twig');
    }



	//start base

	public function getStateColors($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/state_colors.twig');
    }

	public function getTypography($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/typography.twig');
    }
	public function getStack($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/stack.twig');
    }

	public function getTable($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/table.twig');
    }

	public function getProgress($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/progress.twig');
    }

	public function getModal($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/modal.twig');
    }

	public function getAlert($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/alert.twig');
    }

	public function getPopover($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/popover.twig');
    }

	public function getTooltip($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/tooltip.twig');
    }

	public function getBlockui($request, $response)
	{

		return $this->_view->render($response,'admin/partial/base/blockui.twig');
    }
	
	
}