<?php
namespace App\Controllers;

use App\Core\Controller;

class AboutController extends Controller {
    private $settingModel;

    public function __construct() {
        $this->settingModel = $this->model('Setting');
    }

    public function index() {
        // Get about page settings
        $aboutTitle = $this->settingModel->getSettingValue('about_title', 'Về HeypVietNam');
        $aboutHeroDescription = $this->settingModel->getSettingValue('about_hero_description', 'Chúng tôi là thương hiệu sản phẩm bền vững hàng đầu Việt Nam, mang đến các giải pháp xanh cho cuộc sống hàng ngày.');
        
        $aboutStoryTitle = $this->settingModel->getSettingValue('about_story_title', 'Câu Chuyện Của Chúng Tôi');
        $aboutStoryContent = $this->settingModel->getSettingValue('about_story_content', 'HeypVietNam được thành lập từ tình yêu với môi trường và mong muốn tạo ra những sản phẩm bền vững, thân thiện với thiên nhiên, đồng thời không ảnh hưởng đến sinh hoạt hàng ngày của bạn.');
        
        $aboutMissionTitle = $this->settingModel->getSettingValue('about_mission_title', 'Sứ Mệnh Của Chúng Tôi');
        $aboutMissionContent = $this->settingModel->getSettingValue('about_mission_content', 'Tại HeypVietNam, chúng tôi tin rằng mỗi hành động nhỏ đều có thể tạo nên sự thay đổi lớn.');
        
        $aboutValuesTitle = $this->settingModel->getSettingValue('about_values_title', 'Giá Trị Cốt Lõi');
        
        $aboutCTA = $this->settingModel->getSettingValue('about_cta', 'Đồng Hành Cùng Chúng Tôi');
        $aboutCTADescription = $this->settingModel->getSettingValue('about_cta_description', 'Hãy cùng HeypVietNam xây dựng một tương lai xanh hơn, bền vững hơn. Mỗi sản phẩm bạn chọn là một bước tiến nhỏ trong hành trình bảo vệ môi trường.');
        $aboutCTAButton = $this->settingModel->getSettingValue('about_cta_button', 'Khám Phá Sản Phẩm');

        $data = [
            'title' => $aboutTitle,
            'hero_description' => $aboutHeroDescription,
            'story_title' => $aboutStoryTitle,
            'story_content' => $aboutStoryContent,
            'mission_title' => $aboutMissionTitle,
            'mission_content' => $aboutMissionContent,
            'values_title' => $aboutValuesTitle,
            'cta_title' => $aboutCTA,
            'cta_description' => $aboutCTADescription,
            'cta_button' => $aboutCTAButton
        ];

        $this->viewWithLayout('pages/about', $data);
    }
} 
