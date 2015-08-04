<?php
class VendorRepo{

	// check if Business Name exists, returns false else returns true
	public function checkVendor($request)
	{	// initial response is bad
		$response = false;
		
		$requestData = $request;
		//checks if business_name already exists
		if(!empty($request['vendor_id']))
			$count = $GLOBALS['con']->from('vendors')->where('business_name',$requestData['business_name'])->where('address',$requestData['address'])->where('id != ?', $requestData['vendor_id'])->count();
		else
			$count = $GLOBALS['con']->from('vendors')->where('business_name',$requestData['business_name'])->where('address',$requestData['address'])->count();


		if($count > 0)
		{
			$response = false;
		}
		else
		{
			$response = true;
		}

		return $response;
	}

	public function getVendors($request)
	{
		$sortBy = 'id';
		$orderBy = 'asc';

		if(isset($request['sort_by']) && !empty($request['sort_by']) && isset($request['sort_order']) && !empty($request['sort_order'] )) 
		{
			$sortBy = $request['sort_by'];
			$orderBy = $request['sort_order'];
		}

		$count = 0;
		if(!isset($request['status']))
			$status = 'activated';
		else
			$status = $request['status'];

		if(!isset($request['limit']))
			$limit = 15;
		else
			$limit = $request['limit'];

		$total_pages = 0;
		if(!isset($request['page']))
			$page = 0;
		else
			$page = $request['page'];

		$offset = $page * $limit;

		$resp = array('code' => 200, 'data' => array());

		if(isset($request['search']) && !empty($request['search']))
			$key = '%'.$request['search'].'%';

		if(isset($request['search']) && !empty($request['search']) && ($request['promo'] == false))
		{
			$vendors = $GLOBALS['con']->from('vendors')->where("business_name LIKE ?", $key);
		}
		else if(isset($request['vendor_id']))
		{
			$vendors = $GLOBALS['con']->from('vendors')->where("id", $request['vendor_id']);
		}
		else
		{
			if(!isset($key))
			{
				$count = $GLOBALS['con']->from('vendors')->where("status", $status)->count();
			}
			else
			{
				$rawSql = "SELECT COUNT(*) as cid FROM vendors where status = '".$status."' AND ( 
					first_name like '".$key."' || 
					last_name like '".$key."' || 
					business_name like '".$key."' || 
					address like '".$key."' || 
					city like '".$key."' || 
					state like '".$key."' || 
					country like '".$key."' || 
					postcode like '".$key."' || 
					office_number like '".$key."' || 
					cell_number like '".$key."' || 
					email like '".$key."' || 
					website like '".$key."'
					)";
				$stmt = $GLOBALS['pdo']->query($rawSql);
				$count = $stmt->fetchColumn();
//				$count = $stmt->fetchAll(PDO::FETCH_ASSOC);


			}
			$total_pages = ceil($count / $limit);
			if(!isset($key))
			{
				$vendors = $GLOBALS['con']->from('vendors')->where("status", $status)->orderBy($sortBy." ".$orderBy)->limit($limit)->offset($offset);
			}
			else
			{
				$rawSql = "SELECT * FROM vendors where status = '".$status."' AND ( 
					first_name like '".$key."' || 
					last_name like '".$key."' || 
					business_name like '".$key."' || 
					address like '".$key."' || 
					city like '".$key."' || 
					state like '".$key."' || 
					country like '".$key."' || 
					postcode like '".$key."' || 
					office_number like '".$key."' || 
					cell_number like '".$key."' || 
					email like '".$key."' || 
					website like '".$key."'
					)";
				$stmt = $GLOBALS['pdo']->query($rawSql);
				$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}

		}

		$allVendors = array();
		if(!empty($vendors))
		{
			foreach ($vendors as $key => $vendor) {
				$vendor['images'] = $this->getVendorImages($vendor['id']);
				$vendor['days'] = $this->getVendorDays($vendor['id']);
		
				if(!isset($request['vendor_id']))
					$resp['data'][] = $vendor;
				else
					$resp['data'] = $vendor;
			}
		}

		$resp['total_pages'] = $total_pages;
		$resp['count'] = $count;

		return $resp;
	}

	public function businessAdd($request)
	{
		// initial response is bad
		$response = 400;
		$requestData = $request;
		//var_dump($requestData);
		$check = $this->checkVendor($requestData);

		if($check)
		{
			$values = array('first_name' 		=> $requestData['first_name'],
							'last_name' 		=> $requestData['last_name'],
							'business_name' 	=> $requestData['business_name'],
							'address' 			=> $requestData['address'],
							'city'				=> $requestData['city'],
							'state' 			=> $requestData['state'],
							'country' 			=> $requestData['country'],
							'postcode' 			=> $requestData['postcode'],
							'cat_id' 			=> $requestData['cat_id'],
							'sub_cat_id'		=> $requestData['sub_cat_id'],
							'office_number' 	=> $requestData['office_number'],
							'cell_number' 		=> $requestData['cell_number'],
							'email' 			=> $requestData['email'], 
							'website' 			=> $requestData['website'],
							'facebook' 			=> $requestData['facebook'],
							'youtube' 			=> $requestData['youtube'],
							'twitter' 			=> $requestData['twitter'],
							'instagram' 		=> $requestData['instagram'],
							'status' 			=> 'pending',
							'date_created' 		=> date("Y-m-d H:i:s"));
			//print_r($values);

			if(!empty($requestData['vendor_id']))
			{
				unset($values['status']);
				$query = $GLOBALS['con']->deleteFrom('vendor_images')->where('vendor_id', $requestData['vendor_id'])->execute();	
				$query = $GLOBALS['con']->deleteFrom('vendor_working_days')->where('vendor_id', $requestData['vendor_id'])->execute();	

				$rs = $GLOBALS['con']->update('vendors', $values, $requestData['vendor_id'])->execute();			
				$vendorId = $requestData['vendor_id'];
			}
			else
			{	
				// Add vendor
				$loginRepo = new LoginRepo();
				$admindata = $loginRepo->getAdminData(1);

				//"coursemadt@gmail.com"
				$to = $admindata['username'];
				$subject = "New Business Added";

				$message = "
				<html>
				<head>
				<title>New Vendor</title>
				</head>
				<body>
				Hello,  New business is added. Please review the details. 
				<table cellspacing='20'>
				<tr>
				<th>".$requestData['first_name'].' '.$requestData['last_name']."</th>
				<th>".$requestData['business_name']."</th>
				</tr>
				<tr>
				<td colspan='2'><a href='".$GLOBALS['abslolute_path']."'>Login into admin</a></td>
				</tr>
				</table>
				</body>
				</html>
				";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$admindata['username'].'>' . "\r\n";

mail($to,$subject,$message,$headers);				
				$vendorId = $GLOBALS['con']->insertInto('vendors', $values)->execute();
			}


			if($vendorId > 0)
			{
				if(isset($requestData['images']) && !empty($requestData['images']))
					$this->addVendorImages($vendorId, $requestData['images']);

				if(isset($requestData['days']) && !empty($requestData['days']))
					$this->addVendorDays($vendorId, $requestData['days']);							
			}
			$response = 200;
		}
		else
		{
			$response = 400;
		}

		return $response;
	}

	// Add Vendor Images
	public function addVendorImages($vendorId, $images)
	{
		if(!empty($images))
		{
			foreach($images as $image)
			{
				$values = array('vendor_id' => $vendorId, 'path' => $image);
				$query = $GLOBALS['con']->insertInto('vendor_images', $values)->execute();
			}
		}
	}

	public function getVendorDays($vendorId)
	{
		$VendorDays = array();
		if(!empty($vendorId))
		{
			$days = $GLOBALS['con']->from('vendor_working_days')->where('vendor_id',$vendorId);

			foreach($days as $day)
			{
				$day['start_time'] = date("g:i A", strtotime($day['start_time']));
				$day['end_time'] = date("g:i A", strtotime($day['end_time']));				
				$VendorDays[] = $day;
			}
		}

		return $VendorDays;
	}
	
	public function getVendorImages($vendorId)
	{
		$VendorImages = array();
		if(!empty($vendorId))
		{
			$images = $GLOBALS['con']->from('vendor_images')->where('vendor_id',$vendorId);

			foreach($images as $image)
			{
				$image['url'] = UtilityRepo::getRootPath(false).'data/vendor_images/'.$image['path'];
				$VendorImages[] = $image;
			}
		}
		return $VendorImages;
	}

	public function addVendorDays($vendorId, $days)
	{
		if(!empty($days))
		{
			foreach ($days as $key => $day) {
				$values = array('vendor_id' => $vendorId, 'day_code' => $day['day_code'],'start_time' => date("H:i:s", strtotime($day['start_time'])) ,'end_time' => date("H:i:s", strtotime($day['end_time'])));
				$query = $GLOBALS['con']->insertInto('vendor_working_days', $values)->execute();
			}
		}

	}

	public function vendorStatus($request)
	{
		$reponse = 400;
		$requestData = $request;
		if(!empty($requestData['id']))
		{
			$value = array('status' => $requestData['status']); 
			$query = $GLOBALS['con']->update('vendors',$value,$requestData['id'])->execute();

			if($requestData['status'] == 'deactivated')
			{
				// remove vendor from promo deals and deactivate promo
				$query = $GLOBALS['con']->update('promo_vendors')->set(array('status' => 'deactivated'))->where('vendor_id', $requestData['id'])->execute();

				$query3 =  $GLOBALS['con']->deleteFrom('vendor_deals')->where('vendor_id', $requestData['id'])->execute();
			}

			$reponse = 200;
			
		}
		return $reponse;
	}
	
	public function deleteVendor($request)
	{
		$requestData = $request;
		$response = 400;

		$exists = $this->count($requestData['id']);
		if($exists)
		{
			$query = $GLOBALS['con']->deleteFrom('vendors')->where('id', $requestData['id'])->execute();
			$query1 =  $GLOBALS['con']->deleteFrom('vendor_images')->where('vendor_id', $requestData['id'])->execute();
			$query2 =  $GLOBALS['con']->deleteFrom('vendor_working_days')->where('vendor_id', $requestData['id'])->execute();
			$query3 =  $GLOBALS['con']->deleteFrom('vendor_deals')->where('vendor_id', $requestData['id'])->execute();
			$query4 =  $GLOBALS['con']->deleteFrom('promo_vendors')->where('vendor_id', $requestData['id'])->execute();
			$query4 =  $GLOBALS['con']->deleteFrom('promo_vendor_images')->where('vendor_id', $requestData['id'])->execute();

			$response = 200;

		}
		else
		{
			$response = 400;
		}
		return $response;


	}

	public function count($request)
	{

		$query = $GLOBALS['con']->from('vendors')->where('id', $request);
		$count = $query->count();
		 return $count;
	}
	

}