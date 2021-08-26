<?php
class ControllerRepairFaq extends Controller {

	public function index() {
		$this->load->language('repair/faq');
		$this->load->model('repair/faq');

		$data['faq'] = $this->model_repair_faq->getFaqMock();

		return $this->load->view('repair/faq', $data);
	}

}
