<?php

namespace ovabrw_product_elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class ovabrw_search extends Widget_Base {


	public function get_name() {		
		return 'ovabrw_search';
	}

	public function get_title() {
		return esc_html__( 'Search Product', 'ova-brw' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return [ 'ovatheme' ];
	}

	public function get_script_depends() {
		return [ 'script-elementor' ];
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_setting',
			[
				'label' => esc_html__( 'Settings', 'ova-brw' ),
			]
		);

			$this->add_control(
				'search_result',
				[
					'label' => esc_html__( 'Search Result Page', 'ova-brw' ),
					'type' => \Elementor\Controls_Manager::SELECT,
					'default' => 'default',
					'options' => [
						'default'  	=> esc_html__( 'Default', 'ova-brw' ),
						'new_page' 	=> esc_html__( 'New Page', 'ova-brw' ),
					],
				]
			);

			$this->add_control(
				'search_result_url',
				[
					'label' 		=> esc_html__( 'Link', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::URL,
					'placeholder' 	=> esc_html__( 'https://your-link.com', 'ova-brw' ),
					'dynamic' 		=> [
						'active' => true,
					],
					'default' => [
						'url' 			=> '#',
						'is_external' 	=> false,
						'nofollow' 		=> false,
					],
					'condition' => [
						'search_result' => 'new_page',
					],
				]
			);

			$this->add_control(
				'button_label',
				[
					'label' 	=> esc_html__( 'Button Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
				]
			);

			$this->add_control(
				'icon_button',
				[
					'label' 	=> __( 'Icon Button', 'ova-brw' ),
					'type' 		=> Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-search',
						'library' 	=> 'all',
					],
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_destinations',
			[
				'label' => esc_html__( 'Destinations', 'ova-brw' ),
			]
		);

		    $this->add_control(
				'icon_destination',
				[
					'label' 	=> __( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-location',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_control(
				'destination_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Destinations', 'ova-brw' ),
				]
			);

			$this->add_control(
				'destination_placeholder',
				[
					'label' 	=> esc_html__( 'Placeholder', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Where are you going?', 'ova-brw' ),
				]
			);

			$this->add_control(
				'destination_default',
				[
					'label' 	=> esc_html__( 'Default', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::SELECT,
					'options' 	=> ovabrw_get_destinations(),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_taxonomy',
			[
				'label' => esc_html__( 'Custom Taxonomy', 'ova-brw' ),
			]
		);

			$taxonomies 		= get_option( 'ovabrw_custom_taxonomy', array() );
			$data_taxonomy[''] 	= esc_html__( 'None', 'ova-brw' );

			if( ! empty( $taxonomies ) && is_array( $taxonomies ) ) {
				foreach( $taxonomies as $key => $value ) {
					$data_taxonomy[$key] = $value['name'];
				}
			}

			$this->add_control(
				'slug_custom_taxonomy', [
					'label' 		=> esc_html__( 'Select Custom Taxonomy', 'ova-brw' ),
					'type' 			=> \Elementor\Controls_Manager::SELECT,
					'label_block' 	=> true,
					'options' 		=> $data_taxonomy,
				]
			);

			$this->add_control(
				'icon_custom_taxonomy',
				[
					'label' 	=> esc_html__( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-plane',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_control(
				'custom_taxonomy_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Activity', 'ova-brw' ),
				]
			);

			$this->add_control(
				'custom_taxonomy_placeholder',
				[
					'label' 	=> esc_html__( 'Placeholder', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'All Activity', 'ova-brw' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_check_in',
			[
				'label' => esc_html__( 'Check in', 'ova-brw' ),
			]
		);

		    $this->add_control(
				'icon_check_in',
				[
					'label' 	=> esc_html__( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-calander',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_control(
				'check_in_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Dates', 'ova-brw' ),
					'separator' => 'before'
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_adults',
			[
				'label' => esc_html__( 'Adults', 'ova-brw' ),
			]
		);

			$this->add_control(
				'adults_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Adults', 'ova-brw' ),
				]
			);

			$this->add_control(
				'default_adult_number',
				[
					'label' 	=> esc_html__( 'Default Adult Number', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'step' 		=> 1,
					'default' 	=> 2,
				]
			);

			$this->add_control(
				'max_adult',
				[
					'label' 	=> esc_html__( 'Maximum Adult', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'step' 		=> 1,
					'default' 	=> 30,
				]
			);

			$this->add_control(
				'min_adult',
				[
					'label' 	=> esc_html__( 'Minimum Adult', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 1,
					'step' 		=> 1,
					'default' 	=> 1,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_childrens',
			[
				'label' => esc_html__( 'Childrens', 'ova-brw' ),
			]
		);

			$this->add_control(
				'childrens_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Childrens', 'ova-brw' ),
				]
			);

			$this->add_control(
				'default_children_number',
				[
					'label' 	=> esc_html__( 'Default Childrens Number', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 0,
					'step' 		=> 1,
					'default' 	=> 0,
				]
			);

			$this->add_control(
				'max_children',
				[
					'label' 	=> esc_html__( 'Maximum Children', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 0,
					'step' 		=> 1,
					'default' 	=> 10,
				]
			);

			$this->add_control(
				'min_children',
				[
					'label' 	=> esc_html__( 'Minimum Childrens', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::NUMBER,
					'min' 		=> 0,
					'step' 		=> 1,
					'default' 	=> 0,
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_guests',
			[
				'label' => esc_html__( 'Guests', 'ova-brw' ),
			]
		);

		    $this->add_control(
				'icon_guests',
				[
					'label' 	=> esc_html__( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::ICONS,
					'default' 	=> [
						'value' 	=> 'icomoon icomoon-user',
						'library' 	=> 'all',
					],
				]
			);

			$this->add_control(
				'guests_label',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Guests', 'ova-brw' ),
				]
			);

			$this->add_control(
				'guests_placeholder',
				[
					'label' 	=> esc_html__( 'Placeholder', 'ova-brw' ),
					'type' 		=> \Elementor\Controls_Manager::TEXT,
					'default' 	=> esc_html__( 'Persons', 'ova-brw' ),
				]
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ovabrw_search',
			[
				'label' => esc_html__( 'Search Wrapper', 'ova-brw' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		    $this->add_responsive_control(
				'search_max_width',
				[
					'label' 		=> esc_html__( 'Max Width', 'ova-brw' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%'],
					'default' => [
						'unit' => 'px',
					],
					'range' => [
						'px' => [
							'min' => 700,
							'max' => 1290,
							'step' => 5,
						],
						'%' => [
							'min' => 30,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search' => 'max-width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'search_bgcolor',
				[
					'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
	            'search_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
	            'search_margin',
	            [
	                'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'search_border',
					'label' => esc_html__( 'Border', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ovabrw-search',
				]
			);

			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'search_box_shadow',
					'label' => esc_html__( 'Box Shadow', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ovabrw-search',
				]
			);

		$this->end_controls_section();

		/* Begin Label Style */
		$this->start_controls_section(
            'label_style',
            [
                'label' => esc_html__( 'Label', 'ova-brw' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'search_label_typography',
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .label',
				]
			);

			$this->add_control(
				'label_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .label' => 'color: {{VALUE}};',
					],
				]
			);

	        $this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'search_border_between',
					'label' => esc_html__( 'Border', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field:not(:last-child)',
				]
			);

			$this->add_responsive_control(
	            'search_filed_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

            $this->add_control(
				'icon_label_heading',
				[
					'label' 	=> esc_html__( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before'
				]
			);
            
				$this->add_responsive_control(
					'size_label_icon',
					[
						'label' 		=> esc_html__( 'Size', 'ova-brw' ),
						'type' 			=> Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 35,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field i' => 'font-size: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field svg' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'icon_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field i' => 'color: {{VALUE}};',
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field svg, {{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field svg path' => 'fill : {{VALUE}};'
						],
					]
				);

        $this->end_controls_section();
		/* End label style */

		/* Begin Placeholder Style */
		$this->start_controls_section(
            'placeholder_style',
            [
                'label' => esc_html__( 'Placeholder', 'ova-brw' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

			$this->add_control(
				'placeholder_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-input input::placeholder, {{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker .guestspicker, {{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-input select, .ovabrw-search .select2-container--default .select2-selection--single .select2-selection__rendered' => 'color: {{VALUE}};',
					],
				]
			);

        $this->end_controls_section();
		/* End Placeholder style */

		/* Begin Button Style */
		$this->start_controls_section(
            'button_style',
            [
                'label' => esc_html__( 'Search Button', 'ova-brw' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'search_button_typography',
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn',
				]
			);

			$this->add_control(
				'button_text_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_text_color_hover',
				[
					'label' 	=> esc_html__( 'Color Hover', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn:hover' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_bgcolor',
				[
					'label' 	=> esc_html__( 'Backgrund Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'button_bgcolor_hover',
				[
					'label' 	=> esc_html__( 'Backgrund Color Hover', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn:hover' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
	            'button_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_responsive_control(
	            'button_border_radius',
	            [
	                'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'search_button_border',
					'label' => esc_html__( 'Border', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-search-btn button.ovabrw-btn',
				]
			);

        $this->end_controls_section();
		/* End Button style */

		/* Begin guest Style */
		$this->start_controls_section(
            'guest_style',
            [
                'label' => esc_html__( 'Guest', 'ova-brw' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_responsive_control(
				'guest_width',
				[
					'label' 		=> esc_html__( 'Width', 'ova-brw' ),
					'type' 			=> Controls_Manager::SLIDER,
					'size_units' 	=> [ 'px', '%'],
					'range' => [
						'px' => [
							'min' => 200,
							'max' => 450,
							'step' => 1,
						],
						'%' => [
							'min' => 0,
							'max' => 100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .search-field .ovabrw-guestspicker-content' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
            
            $this->add_control(
				'guest_dropdown_heading',
				[
					'label' 	=> esc_html__( 'Guest Dropdown', 'ova-brw' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before'
				]
			);

            $this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' 	 => 'guest_typography',
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .description label',
				]
			);

			$this->add_control(
				'label_guest_color',
				[
					'label' 	=> esc_html__( 'Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .description label' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_responsive_control(
	            'guest_dropdown_padding',
	            [
	                'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
	                'type' 			=> Controls_Manager::DIMENSIONS,
	                'size_units' 	=> [ 'px', '%', 'em' ],
	                'selectors' 	=> [
	                    '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	                ],
	            ]
	        );

	        $this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				[
					'name' => 'guests_border',
					'label' => esc_html__( 'Border', 'ova-brw' ),
					'selector' => '{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content',
				]
			);

			$this->add_control(
				'guest_dropdown_caret_color',
				[
					'label' 	=> esc_html__( 'Caret Color', 'ova-brw' ),
					'type' 		=> Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content:before' => 'border-bottom-color: {{VALUE}};',
					],
				]
			);

            $this->add_control(
				'icon_guest_heading',
				[
					'label' 	=> esc_html__( 'Icon', 'ova-brw' ),
					'type' 		=> Controls_Manager::HEADING,
					'separator' => 'before'
				]
			);
            
				$this->add_responsive_control(
					'size_guest_icon',
					[
						'label' 		=> esc_html__( 'Size', 'ova-brw' ),
						'type' 			=> Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'bgsize_guest_icon',
					[
						'label' 		=> esc_html__( 'Background Size', 'ova-brw' ),
						'type' 			=> Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 50,
								'step' => 1,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs(
					'style_tabs'
				);

				$this->start_controls_tab(
					'style_guests_normal_tab',
					[
						'label' => esc_html__( 'Normal', 'ova-brw' ),
					]
				);

					$this->add_control(
						'icon_guest_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon i' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'icon_guest_bgcolor',
						[
							'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon' => 'background-color: {{VALUE}};',
							],
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'style_guests_hover_tab',
					[
						'label' => esc_html__( 'Hover', 'ova-brw' ),
					]
				);
                     
                    $this->add_control(
						'icon_guest_color_hover',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon:hover i' => 'color: {{VALUE}};',
							],
						]
					);

					$this->add_control(
						'icon_guest_bgcolor_hover',
						[
							'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
							'type' 		=> Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-search .ovabrw-search-form .ovabrw-s-field .search-field .ovabrw-guestspicker-content .guests-buttons .guests-button .guests-icon:hover' => 'background-color: {{VALUE}};',
							],
						]
					);
					

				$this->end_controls_tab();

			$this->end_controls_tabs();
			    
        $this->end_controls_section();
		/* End guest style */


	}

	protected function render() {

		$settings = $this->get_settings();

		$template = apply_filters( 'ovabrw_ft_element_search', 'single/ovabrw_search.php' );

		ob_start();
		ovabrw_get_template( $template, $settings );
		echo ob_get_clean();
	}
}