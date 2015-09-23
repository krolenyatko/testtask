<?php

class CustomerController extends Controller
{
    	public function actionIndex()
	{
            $this->render('index');
	}
        
	public function actionAddCustomer()
	{
            $data = CJSON::decode(file_get_contents('php://input'));
            $customer = new Customer();
            $customer->attributes = $data;
            if ($customer->save()) {
                $this->renderJSON($customer);
            }
            else {
                $response = array('success' => false, 'message' => 'Couldn\'t save customer');
                $this->renderJSON($response);
            }
	}

	public function actionDeleteCustomer($id)
	{
            $customer = Customer::model()->findByPk($id);
            if (!$customer) {
                $response = array('success' => false, 'message' => 'Couldn\'t find customer with id = '.$id);
                $this->renderJSON($response);
                return;
            }
            if ($customer->delete()) {
                $response = array('success' => true);
                $this->renderJSON($response);
            }
            else {
                $response = array('success' => false, 'message' => 'Couldn\'t delete customer with id = '.$id);
                $this->renderJSON($response);
            }
	}

	public function actionEditCustomer($id)
	{
            $data = CJSON::decode(file_get_contents('php://input'));
            $customer = Customer::model()->findByPk($id);
            
            if (!$customer) {
                $response = array('success' => false, 'message' => 'Couldn\'t find customer with id = '.$id);
                $this->renderJSON($response);
            }
            $orders = array();
            if (isset($data['orders'])) {
                $orders = $data['orders'];
                unset($data['orders']);
            }
            $customer->attributes = $data;
            $customer->save();
            if (count($orders)) {
                    $ordersArray = array();
                    
                   foreach ($orders as $order) {
                        $order = array(
                            'customer_id' => $id,
                            'posted_at' => $order['postedAt'],
                            'amount' => $order['amount'],
                            'paid_at' => $order['paidAt']
                        );
                        $orderModel = new Order();
                        $orderModel->attributes = $order;
                        $orderModel->save();
                   }
            }
            
            $response = array('success' => true);
            $this->renderJSON($response);
	}

	public function actionGetCustomersList()
	{
            $criteria = new CDbCriteria();

            $models = Customer::model()->findAll($criteria);
            $customersList = Yii::app()->db->createCommand()
                ->select('c.id,c.name, c.phone, c.address')
                ->from('customer c')
                ->where('1', array())
                ->queryAll();
            $i = 0;
            foreach($customersList as $item) {
                $ordersList = Yii::app()->db->createCommand()
                ->select('o.id, o.posted_at, o.amount, o.paid_at')
                ->from('order o')                
                ->where('o.customer_id=:id', array(':id'=>$item['id']))
                ->queryAll();
                $customersList[$i]['orders'] = $ordersList;
                $i++;
            }           
            
            $this->renderJSON($customersList);
	}
        
        protected function renderJSON($data) {
            header('Content-type: application/json');
            echo CJSON::encode($data);
            Yii::app()->end();
        }
}