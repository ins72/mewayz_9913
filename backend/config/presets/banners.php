<?php

return [
    [
        'key' => 'default_banner_1',
        'section' => 'banner',
        'content' => [
            "title" => "Main \n Heading", 
            "subtitle" => "A clear explanation of the problem you are solving and the value of your brand in a way that resonates with your audience and encourages them to take action. Keep it short, interesting and easy to remember." 
        ],

        'settings' => [
            "banner_style" => "1", 
            "shape_avatar" => 100, 
            "enable_image" => true, 
            "actiontype" => "form", 
            'align' => "left",
            "height" => "500",
            'width' => '75',
            'title' => 'l',
            'enable_image' => true,
            'enable_action' => true,
            'split_title' => true,
        ],

        'section_settings' => [
            'color' => 'accent'
        ],

        'form' => [
            "email" => "Email", 
            "button_name" => "Sign Up", 
            "last_name_enable" => false, 
            "first_name_enable" => false, 
            "phone_enable" => false, 
            "message_enable" => false 
        ],

        'items' => [],
    ],


    [
        'key' => 'contact_banner',
        'section' => 'banner',
        'content' => [
            "title" => "Contact", 
            "subtitle" => "Craft a welcoming message that prompts visitors to reach out for communication." 
        ],

        'settings' => [
            "banner_style" => "1", 
            "shape_avatar" => 100, 
            "enable_image" => true, 
            "actiontype" => "form", 
            'align' => "center",
            "height" => "600",
            'width' => '35',
            'title' => 'xl',
            'enable_image' => false,
            'enable_action' => true,
        ],

        'form' => [
            "email" => "Email", 
            "button_name" => "Submit", 
            "last_name_enable" => true, 
            "first_name_enable" => true, 
            "phone_enable" => true, 
            "message_enable" => true 
        ],

        'items' => [],
    ],
    [
        'key' => 'contact_banner_2',
        'section' => 'banner',
        'content' => [
            "title" => "Contact", 
            "subtitle" => "Craft a welcoming message that prompts visitors to reach out for communication." 
        ],

        'settings' => [
            "banner_style" => "3", 
            "shape_avatar" => 100, 
            "enable_image" => true, 
            "actiontype" => "form", 
            'enable_action' => true,
            "height" => "530",
            'title' => 'xl',
        ],

        'form' => [
            "email" => "Email", 
            "button_name" => "Submit", 
            "last_name_enable" => true, 
            "first_name_enable" => true, 
            "phone_enable" => true, 
            "message_enable" => true 
        ],

        'items' => [],
    ],
];
