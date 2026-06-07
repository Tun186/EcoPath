<?php

class Legal extends Controller {
    public function privacy() {
        $data = ['title' => 'Privacy Policy - EcoPath'];
        $this->view('legal/privacy', $data);
    }

    public function terms() {
        $data = ['title' => 'Terms of Service - EcoPath'];
        $this->view('legal/terms', $data);
    }

    public function ngo_verification() {
        $data = ['title' => 'NGO Verification Standard - EcoPath'];
        $this->view('legal/ngo_verification', $data);
    }
}
