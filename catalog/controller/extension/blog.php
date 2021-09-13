<?php

class ControllerExtensionBlog extends Controller
{
	public function index()
	{

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$this->response->setOutput($this->load->view('extension/blog/blog', $data));
	}

	public function blog_list()
	{
		$this->load->language('extension/module/blog');
		$this->load->model('extension/blog/blog');

		if(isset($this->request->get['category_id'])){
			$id = $this->request->get['category_id'];

			$data['get_categories_meta']= $this->model_extension_blog_blog->getCategoriesMeta($id);
			$info_meta = $data['get_categories_meta'];
			$this->document->setTitle($info_meta['meta_title_category']);
			$this->document->setDescription($info_meta['meta_description_category']);
			$this->document->setKeywords($info_meta['meta_keywords_category']);

			}

		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/blog/blog_list')
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$session_lang = $this->session->data['language'];
		$cur_lang_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "language l WHERE l.code = '" . $session_lang . "'");
		$lang_rows = $cur_lang_data->rows[0];
		$lang_id = $lang_rows["language_id"];

		$limit = 9;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;

		$latest_article_limit = $this->config->get('module_blog_setting_no_of_latest_articles');
		$data['latest_articles'] = $this->model_extension_blog_blog->getLatestArticles($latest_article_limit);

		array_walk($data['latest_articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);

		});

		if(isset($this->request->get['filter_search']) || isset($this->request->get['tag']) || isset($this->request->get['category_id']) || isset($this->request->get['category_name']) )
		{
			if(isset($this->request->get['filter_search'])){
				$filter = $this->request->get['filter_search'];
				$data['articles'] = $this->model_extension_blog_blog->filterArticles($filter, $start, $limit);
				$count = $this->model_extension_blog_blog->searchCountSearch($filter)[0]['count'];
				$totalrecords = $count;
			}

			if(isset($this->request->get['tag'])){
				$filter = $this->request->get['tag'];
				$data['articles'] = $this->model_extension_blog_blog->getArticlesByTags($filter, $start, $limit);
				$count = $this->model_extension_blog_blog->countTags($filter)[0]['count'];
				$totalrecords = $count;
			}

			if(isset($this->request->get['category_id'])){
				$id = $this->request->get['category_id'];
				$data['category_name'] = $this->model_extension_blog_blog->getCategoryName($id);
				$filter = $data['category_name'];
				$data['articles'] = $this->model_extension_blog_blog->articlesByCategory($id, $start, $limit);
				$count = $this->model_extension_blog_blog->countCategory($id)[0]['count'];
				$totalrecords = $count;

			}

			if(empty($data['articles'])){
				$data['noRecord']= '<h3> No result found for '.'"'.$filter.'"'.'</h3>';
				$totalrecords = 0;
			}
			$description = $data['articles'];
			foreach ($description as $key => $value) {
				//$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($description[$key]['description']));
				$translate_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_translate WHERE article_id=" . $key. " AND language_id=".$lang_id);
				if($translate_data->num_rows){
					$rows_translate = $translate_data->rows[0];
					$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($rows_translate['article_description']));
					$description[$key]['title'] = $rows_translate["article_title"];
				} else {
					$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($description[$key]['description']));
				}

			  $description[$key]['description'] = $content;
				}
				$data['articles'] = $description ;
			}
		else{
			$data['articles']=$this->model_extension_blog_blog->getArticle($start, $limit);
			$description = $data['articles'];

			foreach ($description as $key => $value) {
                $translate_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_translate WHERE article_id=".$value["article_id"]." AND language_id=".$lang_id);
				if($translate_data->num_rows){
					$rows_translate = $translate_data->rows[0];
					$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($rows_translate['article_description']));
					$description[$key]['title'] = $rows_translate["article_title"];
				} else {
					$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($description[$key]['description']));
				}

			  $description[$key]['description'] = $content;
			}
			$data['articles'] = $description ;

			$totalrecords= $this->model_extension_blog_blog->getArticlesTotalCount();
		}


		$article_info = $data['articles'];

		foreach ($article_info as $key => $value) {
			$article_info[$key]['blog_image'] = $this->model_tool_image->resize($article_info[$key]['blog_image'], 350, 243);
		}

		$data['articles'] = $article_info;

		array_walk($data['articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);

		});

		if($totalrecords !=0 ){
		$url = $this->url->link('extension/blog/blog_list', '&page={page}', true);
		$text_next = $this->language->get('text_next');
		$text_prev = $this->language->get('text_prev');

		$html_pagination = $this->render_pagination($totalrecords, $page, $limit, $url, $text_next, $text_prev, 5);
		$data['pagination'] = $html_pagination;


		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));
		}

		$data['search_article_url']= $this->url->link('extension/blog/autocomplete');
		$data['search_article_url2']= $this->url->link('extension/blog/blog_list');
		$data['get_single_article']= $this->url->link('extension/blog/article');
		$data['heading_title'] = $this->language->get('heading_title');

		$this->response->setOutput($this->load->view('extension/blog/blog_list', $data));
	}

	public function article(){
		$this->load->language('extension/module/blog');
		$this->load->model('extension/blog/blog');
		// new changes for meta title , meta description , meta keywords
		$article_id = $this->request->get['article_id'];
		$data['single_article']= $this->model_extension_blog_blog->getSingleArticle($article_id);

		$info_data = $data['single_article'] ;
		$this->document->setTitle($info_data['meta_title']);
		$this->document->setDescription($info_data['meta_description']);
		$this->document->setKeywords($info_data['meta_keywords']);
		// End changes
		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$data['get_single_article']= $this->url->link('extension/blog/article');
		$data['save_comment_url'] = $this->url->link('extension/blog/saveComment');
		$data['save_reply_url'] = $this->url->link('extension/blog/saveReply');
		$data['get_replies_url'] = $this->url->link('extension/blog/getReplies');
		$data['search_article_url']= $this->url->link('extension/blog/autocomplete');
		$data['search_article_url2'] = $this->url->link('extension/blog/blog_list');
		$data['user_login'] = $this->url->link('account/login');


		//pagination
		$data['allow_comments'] = $this->config->get('module_blog_setting_allow_comments');

		$limit = 5;
		$page=isset($this->request->get['page'])?$this->request->get['page']:1;
		$start= ($page - 1) * $limit;
		$totalrecords=$this->model_extension_blog_blog->countArticleComments($article_id);
		$data['countComments'] = $totalrecords;
		$data['comments'] = $this->model_extension_blog_blog->getComments($article_id, $start, $limit);

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

				$products = $this->model_extension_blog_blog->getArticleProducts($article_id);
				foreach ($products as $product_info ) {
					$image  = $this->model_tool_image->resize($product_info['image'], 200, 200);

				if ($product_info) {
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float) $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					$data['products'][] = array(
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'tax'         => $tax,
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}

		$pagination = new Pagination();
		$pagination->total = $totalrecords;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('extension/blog/article&article_id='.$article_id, '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($totalrecords) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($totalrecords - $limit)) ? $totalrecords : ((($page - 1) * $limit) + $limit), $totalrecords, ceil($totalrecords / $limit));
		//end pagination

		$description = $data['single_article'];
		$session_lang = $this->session->data['language'];
		$cur_lang_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "language l WHERE l.code = '" . $session_lang . "'");
		$lang_rows = $cur_lang_data->rows[0];
		$lang_id = $lang_rows["language_id"];
		$translate_data = $this->db->query("SELECT * FROM " . DB_PREFIX . "article_translate WHERE article_id=" . $article_id. " AND language_id=".$lang_id);
		if($translate_data->num_rows){
			$rows_translate = $translate_data->rows[0];
			$description['description'] = html_entity_decode($rows_translate['article_description']);
			$description['title'] = $rows_translate["article_title"];
		} else {
			$description['description'] = html_entity_decode($description['description']);
		}

		$data['single_article'] = $description ;

		$data['logged_id'] = $this->customer->getId();
		$article_info = $data['single_article'];
		$article_info['blog_image'] = $this->model_tool_image->resize($article_info['blog_image'], 1140, 1140);
		$data['single_article'] = $article_info;
		$content = preg_replace("/<img[^>]+\>/i", "", html_entity_decode($article_info['description']));
		$data['short_desc'] = $content;
		$latest_article_limit = $this->config->get('module_blog_setting_no_of_latest_articles');
		$data['latest_articles'] = $this->model_extension_blog_blog->getLatestArticles($latest_article_limit);

		array_walk($data['latest_articles'],function(&$key){
			$key['href']=$this->url->link('extension/blog/article','article_id='.$key['article_id']);

		});
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/blog/blog_list')
		);
		$data['breadcrumbs'][] = array(
			'text' => $article_info["title"],
			'href' => ''
		);

		$data['user_name']=$this->customer->getFirstName(). " " . $this->customer->getLastName();
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');

		$viewed = $this->load->controller('extension/module/viewed');

						$data["viewed"] = $viewed;

		$this->response->setOutput($this->load->view('extension/blog/blog', $data));

	}



	public function saveComment()
	{

		$customer_name=$this->customer->getFirstName(). " " . $this->customer->getLastName();
		$article_id = $this->request->post['article_id'];
		$comment = $this->request->post['comment'];
		$status = $this->request->post['status'];
		$data = array(
			'customer_name' => $customer_name,
			'comment_article_id' => $article_id,
			'comment' => $comment,
			'status' => $status
		);
		$this->load->model('extension/blog/blog');
		$data2['id'] = $this->model_extension_blog_blog->saveComment($data);
		$data2['success'] = true;
    	echo json_encode($data2);
	}

	public function saveReply()
	{
		$customer_name=$this->customer->getFirstName(). " " . $this->customer->getLastName();
		$comment_id = $this->request->post['comment_id'];
		$article_id = $this->request->post['comment_article_id'];
		$reply = $this->request->post['reply'];
		$status = $this->request->post['status'];

		$data = array(
			'comment_parent_id' => $comment_id,
			'comment_article_id' => $article_id,
			'comment' => $reply,
			'status' => $status,
			'customer_name' => $customer_name
		);

		$this->load->model('extension/blog/blog');
		$data1 = $this->model_extension_blog_blog->saveReply($data);
    	echo json_encode($data1);
	}

	public function getReplies()
	{
		$post_id = $this->request->get['comment_article_id'];
		$comment_id = $this->request->get['comment_id'];
		$this->load->model('extension/blog/blog');
		$replies = $this->model_extension_blog_blog->getReplies($post_id, $comment_id);

		echo json_encode($replies);
	}


	public function search()
		{
			$filter = $this->request->get['filter_search'];
			// var_dump($filter);
		}

	public function autocomplete() {
	$json = array();

    if (isset($this->request->get['filter'])) {

    	$filter = $this->request->get['filter'];
    	$this->load->model('extension/blog/blog');
    	if( (strlen(utf8_decode($filter)) != '') && (strlen(utf8_decode($filter)) >= 3) ){
    		 $results = $this->model_extension_blog_blog->searchArticles($filter);

        foreach ($results as $result) {

            $json[] = array(
                'href' => $this->url->link('extension/blog/article&article_id='.$result['article_id']),
                'title'            => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))

            );
        }
       }
    }

    $sort_order = array();

    foreach ($json as $key => $value) {
        $sort_order[$key] = $value['title'];
    }

    array_multisort($sort_order, SORT_ASC, $json);

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function render_pagination($total, $page, $limit, $url, $text_next, $text_prev, $num_links = 8) {


	if ($page < 1) {
		$page = 1;
	}

	if(!(int)$limit){
		$limit = 10;
	}
	$text_first = '|&lt;';
	$text_last = '&gt;|';
	$num_pages = ceil($total / $limit);

	$url = str_replace('%7Bpage%7D', '{page}', $url);

	$output = '<ul class="pagination">';

	if ($page > 1) {

		if ($page === 1) {
			$output .= '<li class="page-item"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url) . '" class="page-link not-active">' . $text_prev . '</a></li>';
		} else {
			$output .= '<li class="page-item"><a href="' . str_replace('{page}', $page - 1, $url) . '" class="page-link">' . $text_prev . '</a></li>';
		}
	} else {
		$output .= '<li class="page-item"><a href="#"  class="page-link not-active">' . $text_prev . '</a></li>';
	}

	if ($num_pages > 1) {
		if ($num_pages <= $num_links) {
			$start = 1;
			$end = $num_pages;
		} else {
			$start = $page - floor($num_links / 2);
			$end = $page + floor($num_links / 2);

			if ($start < 1) {
				$end += abs($start) + 1;
				$start = 1;
			}

			if ($end > $num_pages) {
				$start -= ($end - $num_pages);
				$end = $num_pages;
			}
		}

		for ($i = $start; $i <= $end; $i++) {
			if ($page == $i) {
				$output .= '<li class="page-item"><a href="#" class="page-link not-active">' . $i . '</a></li>';
			} else {
				if ($i === 1) {
					$output .= '<li class="page-item"><a href="' . str_replace(array('&amp;page={page}', '?page={page}', '&page={page}'), '', $url) . '" class="page-link">' . $i . '</a></li>';
				} else {
					$output .= '<li class="page-item"><a href="' . str_replace('{page}', $i, $url) . '" class="page-link">' . $i . '</a></li>';
				}
			}
		}
	}

	if ($page < $num_pages) {
		$output .= '<li class="page-item"><a href="' . str_replace('{page}', $page + 1, $url) . '" class="page-link">' . $text_next . '</a></li>';
		//$output .= '<li class="page-item"><a href="' . str_replace('{page}', $num_pages, $url) . '">' . $text_last . '</a></li>';
	} else {
		$output .= '<li class="page-item"><a href="#" class="page-link not-active">' . $text_next . '</a></li>';
	}

	$output .= '</ul>';

	if ($num_pages > 1) {
		return $output;
	} else {
		return '';
	}
}


}

?>
