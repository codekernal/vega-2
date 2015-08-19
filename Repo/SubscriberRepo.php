<?php

class SubscriberRepo
{
	public function addSubscriber($request)
	{
		$response = 400;
		if(empty($request['subscribed']))
		{
			$subscribed = 'yes';
		}
		else
		{
			$subscribed = $request['subscribed'];
		}
		if(!empty($request['subscriber_email']))
		{
			$values = array('subscriber_email' => $request['subscriber_email'],'subscribed' => $subscribed,'date_created' => date("Y-m-d H:i:s"));
			$query = $GLOBALS['con']->insertInto('subscriber', $values)->execute();
			$response = 200;
		}
		else
		{
			$response = 400;
		}
		return $response;
	}

	public function getSubscriber($request)
		{
			// Initial response is bad request
			$response = 400;

			// If there is some data in json form
			if(!empty($requestData))
			{				
				$subscriber = $GLOBALS['con']->from('subscriber')->where('id',$request['id'])->fetch();
				$data = array();

				$data[] = $subscriber;
				$response = 200;
			}
			
			else
			{
				$subscribers = $GLOBALS['con']->from('subscriber');
				$data = array();

				foreach($subscribers as $subscriber)
		    	{
					$data[] = $subscriber;

				}

				$response = 200;
					
			}
			
			return array('code' => $response,'data' => $data);
			
	}

	public function deleteSubscriber($request)
	{
		$id = $request['id'];
		$response = 400;

		if(!empty($id))
		{
			$exists = $GLOBALS['con']->from('subscriber')->where('id',$id)->count();
			if($exists)
			{
				$query = $GLOBALS['con']->deleteFrom('subscriber')->where('id', $id)->execute();
				$response = 200;
			}
			else
			{
				$response = 400;
			}
		}
		else
		{
			$response = 400;
		}
		return $response;

	}

}
