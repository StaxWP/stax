<?php

namespace Stax\Customizer;

class Config {
	/**
	 * Link color - deprecated.
	 *
	 * @deprecated
	 */
	const MODS_LINK_COLOR = 'stax_link_color';
	/**
	 * Link hover color - deprecated.
	 *
	 * @deprecated
	 */
	const MODS_LINK_HOVER_COLOR           = 'stax_link_hover_color';
	const MODS_GLOBAL_COLORS              = 'stax_global_colors';
	const MODS_TEXT_COLOR                 = 'stax_text_color';
	const MODS_CONTAINER_WIDTH            = 'stax_container_width';
	const MODS_SITEWIDE_CONTENT_WIDTH     = 'stax_sitewide_content_width';
	const MODS_OTHERS_CONTENT_WIDTH       = 'stax_other_pages_content_width';
	const MODS_ARCHIVE_CONTENT_WIDTH      = 'stax_blog_archive_content_width';
	const MODS_SINGLE_CONTENT_WIDTH       = 'stax_single_post_content_width';
	const MODS_SHOP_ARCHIVE_CONTENT_WIDTH = 'stax_shop_archive_content_width';
	const MODS_SHOP_SINGLE_CONTENT_WIDTH  = 'stax_single_product_content_width';
	const MODS_ADVANCED_LAYOUT_OPTIONS    = 'stax_advanced_layout_options';
	const MODS_BUTTON_PRIMARY_STYLE       = 'stax_button_appearance';
	const MODS_BUTTON_SECONDARY_STYLE     = 'stax_secondary_button_appearance';
	const MODS_BUTTON_PRIMARY_PADDING     = 'stax_button_padding';
	/**
	 * Background color - deprecated.
	 *
	 * @deprecated
	 */
	const MODS_BACKGROUND_COLOR            = 'background_color';
	const MODS_BUTTON_SECONDARY_PADDING    = 'stax_secondary_button_padding';
	const MODS_TYPEFACE_GENERAL            = 'stax_typeface_general';
	const MODS_TYPEFACE_H1                 = 'stax_h1_typeface_general';
	const MODS_TYPEFACE_H2                 = 'stax_h2_typeface_general';
	const MODS_TYPEFACE_H3                 = 'stax_h3_typeface_general';
	const MODS_TYPEFACE_H4                 = 'stax_h4_typeface_general';
	const MODS_TYPEFACE_H5                 = 'stax_h5_typeface_general';
	const MODS_TYPEFACE_H6                 = 'stax_h6_typeface_general';
	const MODS_FONT_GENERAL                = 'stax_body_font_family';
	const MODS_FONT_GENERAL_VARIANTS       = 'stax_body_font_family_variants';
	const MODS_FONT_HEADINGS               = 'stax_headings_font_family';
	const MODS_DEFAULT_CONTAINER_STYLE     = 'stax_default_container_style';
	const MODS_SINGLE_POST_CONTAINER_STYLE = 'stax_single_post_container_style';

	const MODS_BUTTON_TYPEFACE           = 'stax_button_typeface';
	const MODS_SECONDARY_BUTTON_TYPEFACE = 'stax_secondary_button_typeface';

	/**
	 * Keys for directional values.
	 *
	 * @var string[]
	 */
	public static $directional_keys = [ 'top', 'right', 'bottom', 'left' ];

	const CUSTOMIZER_COLORS_PRIORITY      = 10;
	const CUSTOMIZER_TYPOGRAPHY_PRIORITY  = 20;
	const CUSTOMIZER_LAYOUT_PRIORITY      = 40;
	const CUSTOMIZER_SINGLE_POST_PRIORITY = 50;
	const CUSTOMIZER_SINGLE_PAGE_PRIORITY = 60;
	const CUSTOMIZER_ARCHIVE_LIST         = 70;
	const CUSTOMIZER_PERFORMANCE          = 90;
	const CUSTOMIZER_404_PRIORITY         = 95;
	const CUSTOMIZER_MISC_PRIORITY        = 97;

	const CSS_PROP_BORDER_COLOR               = 'border-color';
	const CSS_PROP_BACKGROUND_COLOR           = 'background-color';
	const CSS_PROP_COLOR                      = 'color';
	const CSS_PROP_MAX_WIDTH                  = 'max-width';
	const CSS_PROP_BORDER_RADIUS_TOP_LEFT     = 'border-top-left-radius';
	const CSS_PROP_BORDER_RADIUS_TOP_RIGHT    = 'border-top-right-radius';
	const CSS_PROP_BORDER_RADIUS_BOTTOM_RIGHT = 'border-bottom-right-radius';
	const CSS_PROP_BORDER_RADIUS_BOTTOM_LEFT  = 'border-bottom-left-radius';
	const CSS_PROP_BORDER_RADIUS              = 'border-radius';
	const CSS_PROP_BORDER_WIDTH               = 'border-width';
	const CSS_PROP_BORDER                     = 'border';
	const CSS_PROP_FLEX_BASIS                 = 'flex-basis';
	const CSS_PROP_PADDING                    = 'padding';
	const CSS_PROP_PADDING_RIGHT              = 'padding-right';
	const CSS_PROP_PADDING_LEFT               = 'padding-left';
	const CSS_PROP_MARGIN                     = 'margin';
	const CSS_PROP_MARGIN_LEFT                = 'margin-left';
	const CSS_PROP_MARGIN_RIGHT               = 'margin-right';
	const CSS_PROP_MARGIN_TOP                 = 'margin-top';
	const CSS_PROP_MARGIN_BOTTOM              = 'margin-bottom';
	const CSS_PROP_RIGHT                      = 'right';
	const CSS_PROP_LEFT                       = 'left';
	const CSS_PROP_WIDTH                      = 'width';
	const CSS_PROP_HEIGHT                     = 'height';
	const CSS_PROP_MIN_HEIGHT                 = 'min-height';
	const CSS_PROP_FONT_SIZE                  = 'font-size';
	const CSS_PROP_FILL_COLOR                 = 'fill';
	const CSS_PROP_LETTER_SPACING             = 'letter-spacing';
	const CSS_PROP_LINE_HEIGHT                = 'line-height';
	const CSS_PROP_FONT_WEIGHT                = 'font-weight';
	const CSS_PROP_TEXT_TRANSFORM             = 'text-transform';
	const CSS_PROP_FONT_FAMILY                = 'font-family';
	const CSS_PROP_BOX_SHADOW                 = 'box-shadow';
	const CSS_PROP_MIX_BLEND_MODE             = 'mix-blend-mode';
	const CSS_PROP_OPACITY                    = 'opacity';
	const CSS_PROP_GRID_TEMPLATE_COLS         = 'grid-template-columns';

	const CSS_PROP_CUSTOM_BTN_TYPE           = 'btn-type';
	const CSS_PROP_CUSTOM_FONT_WEIGHT_FAMILY = 'btn-type';

	const VAR_BODY_BG                      = [
		'body-bg-h',
		'body-bg-s',
		'body-bg-l',
	];
	const VAR_PRIMARY_COLOR                = [
		'primary-h',
		'primary-s',
		'primary-l',
	];
	const VAR_LIGHT_COLOR                  = [
		'light-h',
		'light-s',
		'light-l',
	];
	const VAR_DARK_COLOR                   = [
		'dark-h',
		'dark-s',
		'dark-l',
	];
	const VAR_LINK_COLOR                   = [
		'link-h',
		'link-s',
		'link-l',
	];
	const VAR_RELATED_COLOR                = [
		'related-h',
		'related-s',
		'related-l',
	];
	const VAR_ARCHIVE_TITLE_COLOR          = [
		'archive-h',
		'archive-s',
		'archive-l',
	];
	const VAR_TEXT_COLOR                   = 'text-color';
	const VAR_HEADING_COLOR                = 'heading-color';
	const VAR_BORDER_COLOR                 = 'border-color';
	const VAR_TEXT_META_COLOR              = 'text-meta-color';
	const VAR_BODY_BG_CONTRAST             = 'body-bg-contrast-color';
	const VAR_PRIMARY_COLOR_CONTRAST       = 'primary-contrast-color';
	const VAR_LIGHT_COLOR_CONTRAST         = 'light-contrast-color';
	const VAR_DARK_COLOR_CONTRAST          = 'dark-contrast-color';
	const VAR_RELATED_COLOR_CONTRAST       = 'related-contrast-color';
	const VAR_ARCHIVE_TITLE_COLOR_CONTRAST = 'archive-contrast-color';

	const OPTION_GENERAL_CONTAINER_BOXED      = 'stax_general_container_boxed';
	const OPTION_GENERAL_CONTAINER_WIDTH      = 'stax_general_container_width';
	const OPTION_GENERAL_CONTAINER_BACKGROUND = 'stax_general_container_background';

	const OPTION_LAYOUT_GENERAL                          = 'stax_layout_general';
	const OPTION_LAYOUT_GENERAL_CONTAINER                = 'stax_layout_general_container';
	const OPTION_LAYOUT_GENERAL_STICKY_PRIMARY_SIDEBAR   = 'stax_layout_general_sticky_primary_sidebar';
	const OPTION_LAYOUT_GENERAL_STICKY_SECONDARY_SIDEBAR = 'stax_layout_general_sticky_secondary_sidebar';
	const OPTION_LAYOUT_GENERAL_SIDEBAR_GAP              = 'stax_layout_general_sidebar_gap';
	const OPTION_LAYOUT_POST                             = 'stax_layout_post';
	const OPTION_LAYOUT_POST_CONTAINER                   = 'stax_layout_post_container';
	const OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR      = 'stax_layout_post_sticky_primary_sidebar';
	const OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR    = 'stax_layout_post_sticky_secondary_sidebar';
	const OPTION_LAYOUT_POST_SIDEBAR_GAP                 = 'stax_layout_post_sidebar_gap';
	const OPTION_LAYOUT_ARCHIVE                          = 'stax_layout_archive';
	const OPTION_LAYOUT_ARCHIVE_CONTAINER                = 'stax_layout_archive_container';
	const OPTION_LAYOUT_ARCHIVE_STICKY_PRIMARY_SIDEBAR   = 'stax_layout_archive_sticky_primary_sidebar';
	const OPTION_LAYOUT_ARCHIVE_STICKY_SECONDARY_SIDEBAR = 'stax_layout_archive_sticky_secondary_sidebar';
	const OPTION_LAYOUT_ARCHIVE_SIDEBAR_GAP              = 'stax_layout_archive_sidebar_gap';

	const OPTION_SINGLE_SHOW_TITLE                        = 'stax_show_title_section';
	const OPTION_SINGLE_POST_NAVIGATION                   = 'stax_single_post_navigation';
	const OPTION_SINGLE_POST_NAVIGATION_POSITION          = 'stax_single_post_navigation_position';
	const OPTION_SINGLE_POST_RELATED_POSTS                = 'stax_single_post_related_posts';
	const OPTION_SINGLE_POST_RELATED_POSTS_TYPE           = 'stax_single_post_related_posts_type';
	const OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL      = 'stax_single_post_related_posts_thumbnail';
	const OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT           = 'stax_single_post_media_panel_height';
	const OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT       = 'stax_single_post_media_panel_max_height';
	const OPTION_SINGLE_POST_TITLE_COLOR                  = 'stax_single_post_media_panel_text';
	const OPTION_SINGLE_POST_MEDIA_PANEL_FADE_TEXT        = 'stax_single_post_media_panel_fade_text';
	const OPTION_SINGLE_POST_TOP_READING_BAR              = 'stax_single_post_top_reading_bar';
	const OPTION_SINGLE_POST_CATEORY_BREADCRUMB           = 'stax_single_post_cateory_breadcrumb';
	const OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION = 'stax_single_post_cateory_breadcrumb_animation';
	const OPTION_SINGLE_POST_TITLE_POSITION               = 'stax_single_post_title_position';
	const OPTION_SINGLE_POST_TITLE_ALIGN                  = 'stax_single_post_title_align';
	const OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN            = 'stax_single_post_title_extra_align';
	const OPTION_SINGLE_POST_TITLE_SIZE                   = 'stax_single_post_title_size';
	const OPTION_SINGLE_POST_TITLE_ANIMTATION             = 'stax_single_post_title_animation';
	const OPTION_SINGLE_POST_IMAGE_FOR_STANDARD           = 'stax_single_post_image_for_standard';
	const OPTION_SINGLE_POST_IMAGE_WIDTH                  = 'stax_single_post_image_width';
	const OPTION_SINGLE_POST_IMAGE_FORMAT                 = 'stax_single_post_image_format';
	const OPTION_SINGLE_POST_IMAGE_OVERLAY                = 'stax_single_post_image_overlay';
	const OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION       = 'stax_single_post_image_overlay_location';
	const OPTION_SINGLE_POST_AUDIO_PANEL                  = 'stax_single_post_audio_panel';
	const OPTION_SINGLE_POST_VIDEO_PANEL                  = 'stax_single_post_video_panel';
	const OPTION_SINGLE_POST_VIDEO_WIDTH                  = 'stax_single_post_video_width';
	const OPTION_SINGLE_POST_VIDEO_OVERLAY                = 'stax_single_post_video_overlay';
	const OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION       = 'stax_single_post_video_overlay_location';
	const OPTION_SINGLE_POST_GALLERY_PANEL                = 'stax_single_post_gallery_panel';
	const OPTION_SINGLE_POST_GALLERY_WIDTH                = 'stax_single_post_gallery_width';
	const OPTION_SINGLE_POST_GALLERY_SLIDES               = 'stax_single_post_gallery_slides';
	const OPTION_SINGLE_POST_GALLERY_OVERLAY              = 'stax_single_post_gallery_overlay';
	const OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION     = 'stax_single_post_gallery_overlay_location';
	const OPTION_SINGLE_POST_META                         = 'stax_single_post_meta';
	const OPTION_SINGLE_POST_META_AUTHOR_AVATAR           = 'stax_single_post_meta_author_avatar';
	const OPTION_SINGLE_POST_META_AUTHOR_NAME             = 'stax_single_post_meta_author_name';
	const OPTION_SINGLE_POST_META_POST_DATE               = 'stax_single_post_meta_post_date';
	const OPTION_SINGLE_POST_META_READING_TIME            = 'stax_single_post_meta_reading_time';
	const OPTION_SINGLE_POST_META_ANIMATION               = 'stax_single_post_meta_animation';
	const OPTION_SINGLE_POST_SHAPES                       = 'stax_single_post_shapes';

	const OPTION_SINGLE_PAGE_BREADCRUMBS = 'stax_single_page_breadcrumbs';
	const OPTION_SINGLE_PAGE_TITLE_SIZE  = 'stax_single_page_title_size';
	const OPTION_SINGLE_PAGE_TITLE_ALIGN = 'stax_single_page_title_align';
	const OPTION_SINGLE_PAGE_SHAPES      = 'stax_single_page_shapes';

	const OPTION_ARCHIVE_LIST_TYPE                             = 'stax_archive_list_type';
	const OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS             = 'stax_archive_list_type_default_img_pos';
	const OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE          = 'stax_archive_list_type_masonry_media_size';
	const OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE      = 'stax_archive_list_type_default_big_media_size';
	const OPTION_ARCHIVE_LIST_STANDARD_IMAGE                   = 'stax_archive_list_standard_image';
	const OPTION_ARCHIVE_LIST_SHOW_CATEGORY                    = 'stax_archive_list_show_category';
	const OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR               = 'stax_archive_list_show_author_avatar';
	const OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME                 = 'stax_archive_list_show_author_name';
	const OPTION_ARCHIVE_LIST_SHOW_POST_DATE                   = 'stax_archive_list_show_post_date';
	const OPTION_ARCHIVE_LIST_MASONRY_FIRST_BIG                = 'stax_archive_list_masonry_first_big';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE     = 'stax_archive_list_masonry_posts_sidebar_mobile';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET     = 'stax_archive_list_masonry_posts_sidebar_tablet';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP    = 'stax_archive_list_masonry_posts_sidebar_desktop';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE  = 'stax_archive_list_masonry_posts_no_sidebar_mobile';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET  = 'stax_archive_list_masonry_posts_no_sidebar_tablet';
	const OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP = 'stax_archive_list_masonry_posts_no_sidebar_desktop';
	const OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE     = 'stax_archive_list_grid_posts_no_sidebar_mobile';
	const OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET     = 'stax_archive_list_grid_posts_no_sidebar_tablet';
	const OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP    = 'stax_archive_list_grid_posts_no_sidebar_desktop';


	const OPTION_MISC_PRELOADER             = 'stax_misc_preloader';
	const OPTION_MISC_DISABLE_PAGE_COMMENTS = 'stax_misc_disable_page_comments';
	const OPTION_MISC_DISABLE_POST_COMMENTS = 'stax_misc_disable_post_comments';
	const OPTION_MISC_POST_UPDATED_DATE     = 'stax_misc_post_updated_date';
	const OPTION_MISC_DEV_MODE              = 'stax_misc_dev_mode';

	const OPTION_PERF_CSS_PRELOAD = 'stax_perf_css_preload';
	const OPTION_LAZY_LOAD_IMAGES = 'stax_lazy_load_images';
	const OPTION_404_FOOTER       = 'stax_404_footer';

	const OPTION_TYPE_VAR = 'var';
	const OPTION_TYPE_CSS = 'css';

	const OPTIONS = [
		self::OPTION_GENERAL_CONTAINER_BOXED               => [
			'default' => false,
		],
		self::OPTION_GENERAL_CONTAINER_WIDTH               => [
			'type'        => self::OPTION_TYPE_VAR,
			'default'     => 1400,
			'input_attrs' => [
				'var'  => 'layout-max-width',
				'unit' => 'px',
			],
			'condition'   => [
				self::OPTION_GENERAL_CONTAINER_BOXED => true,
			],
		],
		self::OPTION_GENERAL_CONTAINER_BACKGROUND          => [
			'type'        => self::OPTION_TYPE_CSS,
			'default'     => '#f4f4f4',
			'input_attrs' => [
				'output' => 'hex',
			],
			'condition'   => [
				self::OPTION_GENERAL_CONTAINER_BOXED => true,
			],
			'css'         => [
				'.layout-boxed' => self::CSS_PROP_BACKGROUND_COLOR,
			],
		],
		self::OPTION_LAYOUT_GENERAL                        => [
			'default' => 'no-side',
		],
		self::OPTION_LAYOUT_GENERAL_CONTAINER              => [
			'default' => 'large',
		],
		self::OPTION_LAYOUT_GENERAL_STICKY_PRIMARY_SIDEBAR => [
			'default' => false,
		],
		self::OPTION_LAYOUT_GENERAL_STICKY_SECONDARY_SIDEBAR => [
			'default' => false,
		],
		self::OPTION_LAYOUT_GENERAL_SIDEBAR_GAP            => [
			'default' => 80,
		],
		self::OPTION_LAYOUT_POST                           => [
			'default' => 'no-side',
		],
		self::OPTION_LAYOUT_POST_CONTAINER                 => [
			'default' => 'small',
		],
		self::OPTION_LAYOUT_POST_STICKY_PRIMARY_SIDEBAR    => [
			'default' => false,
		],
		self::OPTION_LAYOUT_POST_STICKY_SECONDARY_SIDEBAR  => [
			'default' => false,
		],
		self::OPTION_LAYOUT_POST_SIDEBAR_GAP               => [
			'default' => 80,
		],
		self::OPTION_LAYOUT_ARCHIVE                        => [
			'default' => 'sr',
		],
		self::OPTION_LAYOUT_ARCHIVE_CONTAINER              => [
			'default' => 'small',
		],
		self::OPTION_LAYOUT_ARCHIVE_STICKY_PRIMARY_SIDEBAR => [
			'default' => false,
		],
		self::OPTION_LAYOUT_ARCHIVE_STICKY_SECONDARY_SIDEBAR => [
			'default' => false,
		],
		self::OPTION_LAYOUT_ARCHIVE_SIDEBAR_GAP            => [
			'default' => 80,
		],
		self::OPTION_SINGLE_SHOW_TITLE                    => [
			'default' => 'yes',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_NAVIGATION                => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_NAVIGATION_POSITION       => [
			'default' => 'normal',
		],
		self::OPTION_SINGLE_POST_RELATED_POSTS             => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_RELATED_POSTS_TYPE        => [
			'default' => 'category',
		],
		self::OPTION_SINGLE_POST_RELATED_POSTS_THUMBNAIL   => [
			'default' => 'normal',
		],
		self::OPTION_SINGLE_POST_MEDIA_PANEL_HEIGHT        => [
			'default' => '1',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_MEDIA_PANEL_MAX_HEIGHT    => [
			'default' => 800,
		],
		self::OPTION_SINGLE_POST_TITLE_COLOR               => [
			'default' => 'dark',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_MEDIA_PANEL_FADE_TEXT     => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_TOP_READING_BAR           => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_CATEORY_BREADCRUMB        => [
			'default' => 'category-breadcrumb',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_CATEORY_BREADCRUMB_ANIMATION => [
			'default' => 'fadeInDown',
		],
		self::OPTION_SINGLE_POST_TITLE_POSITION            => [
			'default' => 'title-above',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_TITLE_ALIGN               => [
			'default' => 'default',
			'metabox' => [
				'exists'     => true,
				'dependency' => [
					'option' => self::OPTION_SINGLE_POST_TITLE_POSITION,
					'values' => [
						'title-above',
						'title-below',
					],
				],
			],
		],
		self::OPTION_SINGLE_POST_TITLE_EXTRA_ALIGN         => [
			'default' => 'default',
			'metabox' => [
				'exists'     => true,
				'dependency' => [
					'option' => self::OPTION_SINGLE_POST_TITLE_POSITION,
					'values' => [
						'half',
						'title-over',
					],
				],
			],
		],
		self::OPTION_SINGLE_POST_TITLE_SIZE                => [
			'default' => 'default',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_TITLE_ANIMTATION          => [
			'default' => 'fadeIn',
		],
		self::OPTION_SINGLE_POST_IMAGE_FOR_STANDARD        => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_IMAGE_WIDTH               => [
			'default' => 'wide',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_IMAGE_FORMAT              => [
			'default' => 'cover',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_IMAGE_OVERLAY             => [
			'default' => 'none',
		],
		self::OPTION_SINGLE_POST_IMAGE_OVERLAY_LOCATION    => [
			'default' => 'both',
		],
		self::OPTION_SINGLE_POST_AUDIO_PANEL               => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_VIDEO_PANEL               => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_VIDEO_WIDTH               => [
			'default' => 'wide',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_VIDEO_OVERLAY             => [
			'default' => 'none',
		],
		self::OPTION_SINGLE_POST_VIDEO_OVERLAY_LOCATION    => [
			'default' => 'both',
		],
		self::OPTION_SINGLE_POST_GALLERY_PANEL             => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_GALLERY_WIDTH             => [
			'default' => 'wide',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_GALLERY_SLIDES            => [
			'default' => 'portrait',
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_GALLERY_OVERLAY           => [
			'default' => 'none',
		],
		self::OPTION_SINGLE_POST_GALLERY_OVERLAY_LOCATION  => [
			'default' => 'both',
		],
		self::OPTION_SINGLE_POST_META                      => [
			'default' => true,
		],
		self::OPTION_SINGLE_POST_META_AUTHOR_AVATAR        => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_META_AUTHOR_NAME          => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_META_POST_DATE            => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_META_READING_TIME         => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_POST_META_ANIMATION            => [
			'default' => 'fadeInUp',
		],
		self::OPTION_SINGLE_POST_SHAPES                    => [
			'default' => true,
			'metabox' => [
				'exists' => true,
			],
		],
		self::OPTION_SINGLE_PAGE_BREADCRUMBS               => [
			'default' => '1',
		],
		self::OPTION_SINGLE_PAGE_TITLE_SIZE                => [
			'default' => 'default',
		],
		self::OPTION_SINGLE_PAGE_TITLE_ALIGN               => [
			'default' => 'default',
		],
		self::OPTION_SINGLE_PAGE_SHAPES                    => [
			'default' => false,
		],
		self::OPTION_ARCHIVE_LIST_TYPE                     => [
			'default' => 'masonry',
		],
		self::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_IMG_POS     => [
			'default' => 'right',
		],
		self::OPTION_ARCHIVE_LIST_TYPE_MASONRY_MEDIA_SIZE  => [
			'default' => 'normal',
		],
		self::OPTION_ARCHIVE_LIST_TYPE_DEFAULT_BIG_MEDIA_SIZE => [
			'default' => 'normal',
		],
		self::OPTION_ARCHIVE_LIST_STANDARD_IMAGE           => [
			'default' => true,
		],
		self::OPTION_ARCHIVE_LIST_SHOW_CATEGORY            => [
			'default' => true,
		],
		self::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_AVATAR       => [
			'default' => true,
		],
		self::OPTION_ARCHIVE_LIST_SHOW_AUTHOR_NAME         => [
			'default' => true,
		],
		self::OPTION_ARCHIVE_LIST_SHOW_POST_DATE           => [
			'default' => true,
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_FIRST_BIG        => [
			'default' => false,
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_MOBILE => [
			'default' => '12',
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_TABLET => [
			'default' => '6',
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_SIDEBAR_DESKTOP => [
			'default' => '6',
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_MOBILE => [
			'default' => '12',
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_TABLET => [
			'default' => '6',
		],
		self::OPTION_ARCHIVE_LIST_MASONRY_POSTS_NO_SIDEBAR_DESKTOP => [
			'default' => '4',
		],
		self::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_MOBILE => [
			'default' => '12',
		],
		self::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_TABLET => [
			'default' => '12',
		],
		self::OPTION_ARCHIVE_LIST_GRID_POSTS_NO_SIDEBAR_DESKTOP => [
			'default' => '3',
		],
		self::OPTION_MISC_PRELOADER                        => [
			'default' => false,
		],
		self::OPTION_MISC_DISABLE_PAGE_COMMENTS            => [
			'default' => false,
		],
		self::OPTION_MISC_DISABLE_POST_COMMENTS            => [
			'default' => false,
		],
		self::OPTION_MISC_POST_UPDATED_DATE                => [
			'default' => false,
		],
		self::OPTION_MISC_DEV_MODE                         => [
			'default' => false,
		],
		self::OPTION_PERF_CSS_PRELOAD                      => [
			'default' => false,
		],
		self::OPTION_LAZY_LOAD_IMAGES                      => [
			'default' => true,
		],
		self::OPTION_404_FOOTER                            => [
			'default' => false,
		],
	];

	const STANDARD_FONTS = [
		'Arial, Helvetica, sans-serif',
		'Arial Black, Gadget, sans-serif',
		'Bookman Old Style, serif',
		'Comic Sans MS, cursive',
		'Courier, monospace',
		'Georgia, serif',
		'Garamond, serif',
		'Impact, Charcoal, sans-serif',
		'Lucida Console, Monaco, monospace',
		'Lucida Sans Unicode, Lucida Grande, sans-serif',
		'MS Sans Serif, Geneva, sans-serif',
		'MS Serif, New York, sans-serif',
		'Palatino Linotype, Book Antiqua, Palatino, serif',
		'Tahoma, Geneva, sans-serif',
		'Times New Roman, Times, serif',
		'Trebuchet MS, Helvetica, sans-serif',
		'Verdana, Geneva, sans-serif',
		'Paratina Linotype',
		'Trebuchet MS',
	];

	const GOOGLE_FONTS = [
		'ABeeZee',
		'Abel',
		'Abhaya Libre',
		'Abril Fatface',
		'Aclonica',
		'Acme',
		'Actor',
		'Adamina',
		'Advent Pro',
		'Aguafina Script',
		'Akronim',
		'Aladin',
		'Alata',
		'Alatsi',
		'Aldrich',
		'Alef',
		'Alegreya',
		'Alegreya SC',
		'Alegreya Sans',
		'Alegreya Sans SC',
		'Aleo',
		'Alex Brush',
		'Alfa Slab One',
		'Alice',
		'Alike',
		'Alike Angular',
		'Allan',
		'Allerta',
		'Allerta Stencil',
		'Allura',
		'Almarai',
		'Almendra',
		'Almendra Display',
		'Almendra SC',
		'Amarante',
		'Amaranth',
		'Amatic SC',
		'Amethysta',
		'Amiko',
		'Amiri',
		'Amita',
		'Anaheim',
		'Andada',
		'Andika',
		'Angkor',
		'Annie Use Your Telescope',
		'Anonymous Pro',
		'Antic',
		'Antic Didone',
		'Antic Slab',
		'Anton',
		'Arapey',
		'Arbutus',
		'Arbutus Slab',
		'Architects Daughter',
		'Archivo',
		'Archivo Black',
		'Archivo Narrow',
		'Aref Ruqaa',
		'Arima Madurai',
		'Arimo',
		'Arizonia',
		'Armata',
		'Arsenal',
		'Artifika',
		'Arvo',
		'Arya',
		'Asap',
		'Asap Condensed',
		'Asar',
		'Asset',
		'Assistant',
		'Astloch',
		'Asul',
		'Athiti',
		'Atma',
		'Atomic Age',
		'Aubrey',
		'Audiowide',
		'Autour One',
		'Average',
		'Average Sans',
		'Averia Gruesa Libre',
		'Averia Libre',
		'Averia Sans Libre',
		'Averia Serif Libre',
		'B612',
		'B612 Mono',
		'Bad Script',
		'Bahiana',
		'Bahianita',
		'Bai Jamjuree',
		'Balsamiq Sans',
		'Baloo',
		'Baloo 2',
		'Baloo Bhai',
		'Baloo Bhai 2',
		'Baloo Bhaijaan',
		'Baloo Bhaina',
		'Baloo Bhaina 2',
		'Baloo Chettan',
		'Baloo Chettan 2',
		'Baloo Da',
		'Baloo Da 2',
		'Baloo Paaji',
		'Baloo Paaji 2',
		'Baloo Tamma',
		'Baloo Tamma 2',
		'Baloo Tammudu',
		'Baloo Tammudu 2',
		'Baloo Thambi',
		'Baloo Thambi 2',
		'Balthazar',
		'Bangers',
		'Barlow',
		'Barlow Condensed',
		'Barlow Semi Condensed',
		'Barriecito',
		'Barrio',
		'Basic',
		'Baskervville',
		'Battambang',
		'Baumans',
		'Bayon',
		'Be Vietnam',
		'Bebas Neue',
		'Belgrano',
		'Bellefair',
		'Belleza',
		'Bellota',
		'Bellota Text',
		'BenchNine',
		'Bentham',
		'Berkshire Swash',
		'Beth Ellen',
		'Bevan',
		'Big Shoulders Display',
		'Big Shoulders Text',
		'Bigelow Rules',
		'Bigshot One',
		'Bilbo',
		'Bilbo Swash Caps',
		'BioRhyme',
		'BioRhyme Expanded',
		'Biryani',
		'Bitter',
		'Black And White Picture',
		'Black Han Sans',
		'Black Ops One',
		'Blinker',
		'Bokor',
		'Bonbon',
		'Boogaloo',
		'Bowlby One',
		'Bowlby One SC',
		'Brawler',
		'Bree Serif',
		'Bubblegum Sans',
		'Bubbler One',
		'Buda',
		'Buenard',
		'Bungee',
		'Bungee Hairline',
		'Bungee Inline',
		'Bungee Outline',
		'Bungee Shade',
		'Butcherman',
		'Butterfly Kids',
		'Cabin',
		'Cabin Condensed',
		'Cabin Sketch',
		'Caesar Dressing',
		'Cagliostro',
		'Cairo',
		'Caladea',
		'Calistoga',
		'Calligraffitti',
		'Cambay',
		'Cambo',
		'Candal',
		'Cantarell',
		'Cantata One',
		'Cantora One',
		'Capriola',
		'Cardo',
		'Carme',
		'Carrois Gothic',
		'Carrois Gothic SC',
		'Carter One',
		'Catamaran',
		'Caudex',
		'Caveat',
		'Caveat Brush',
		'Cedarville Cursive',
		'Ceviche One',
		'Chakra Petch',
		'Changa',
		'Changa One',
		'Chango',
		'Charm',
		'Charmonman',
		'Chathura',
		'Chau Philomene One',
		'Chela One',
		'Chelsea Market',
		'Chenla',
		'Cherry Cream Soda',
		'Cherry Swash',
		'Chewy',
		'Chicle',
		'Chilanka',
		'Chivo',
		'Chonburi',
		'Cinzel',
		'Cinzel Decorative',
		'Clicker Script',
		'Coda',
		'Coda Caption',
		'Codystar',
		'Coiny',
		'Combo',
		'Comfortaa',
		'Comic Neue',
		'Coming Soon',
		'Concert One',
		'Condiment',
		'Content',
		'Contrail One',
		'Convergence',
		'Cookie',
		'Copse',
		'Corben',
		'Cormorant',
		'Cormorant Garamond',
		'Cormorant Infant',
		'Cormorant SC',
		'Cormorant Unicase',
		'Cormorant Upright',
		'Courgette',
		'Courier Prime',
		'Cousine',
		'Coustard',
		'Covered By Your Grace',
		'Crafty Girls',
		'Creepster',
		'Crete Round',
		'Crimson Pro',
		'Crimson Text',
		'Croissant One',
		'Crushed',
		'Cuprum',
		'Cute Font',
		'Cutive',
		'Cutive Mono',
		'DM Mono',
		'DM Sans',
		'DM Serif Display',
		'DM Serif Text',
		'Damion',
		'Dancing Script',
		'Dangrek',
		'Darker Grotesque',
		'David Libre',
		'Dawning of a New Day',
		'Days One',
		'Dekko',
		'Delius',
		'Delius Swash Caps',
		'Delius Unicase',
		'Della Respira',
		'Denk One',
		'Devonshire',
		'Dhurjati',
		'Didact Gothic',
		'Diplomata',
		'Diplomata SC',
		'Do Hyeon',
		'Dokdo',
		'Domine',
		'Donegal One',
		'Doppio One',
		'Dorsa',
		'Dosis',
		'Dr Sugiyama',
		'Droid Sans',
		'Droid Sans Mono',
		'Droid Serif',
		'Duru Sans',
		'Dynalight',
		'EB Garamond',
		'Eagle Lake',
		'East Sea Dokdo',
		'Eater',
		'Economica',
		'Eczar',
		'El Messiri',
		'Electrolize',
		'Elsie',
		'Elsie Swash Caps',
		'Emblema One',
		'Emilys Candy',
		'Encode Sans',
		'Encode Sans Condensed',
		'Encode Sans Expanded',
		'Encode Sans Semi Condensed',
		'Encode Sans Semi Expanded',
		'Engagement',
		'Englebert',
		'Enriqueta',
		'Epilogue',
		'Erica One',
		'Esteban',
		'Euphoria Script',
		'Ewert',
		'Exo',
		'Exo 2',
		'Expletus Sans',
		'Fahkwang',
		'Fanwood Text',
		'Farro',
		'Farsan',
		'Fascinate',
		'Fascinate Inline',
		'Faster One',
		'Fasthand',
		'Fauna One',
		'Faustina',
		'Federant',
		'Federo',
		'Felipa',
		'Fenix',
		'Finger Paint',
		'Fira Code',
		'Fira Mono',
		'Fira Sans',
		'Fira Sans Condensed',
		'Fira Sans Extra Condensed',
		'Fjalla One',
		'Fjord One',
		'Flamenco',
		'Flavors',
		'Fondamento',
		'Fontdiner Swanky',
		'Forum',
		'Francois One',
		'Frank Ruhl Libre',
		'Freckle Face',
		'Fredericka the Great',
		'Fredoka One',
		'Freehand',
		'Fresca',
		'Frijole',
		'Fruktur',
		'Fugaz One',
		'GFS Didot',
		'GFS Neohellenic',
		'Gabriela',
		'Gaegu',
		'Gafata',
		'Galada',
		'Galdeano',
		'Galindo',
		'Gamja Flower',
		'Gayathri',
		'Gelasio',
		'Gentium Basic',
		'Gentium Book Basic',
		'Geo',
		'Geostar',
		'Geostar Fill',
		'Germania One',
		'Gidugu',
		'Gilda Display',
		'Girassol',
		'Give You Glory',
		'Glass Antiqua',
		'Glegoo',
		'Gloria Hallelujah',
		'Goblin One',
		'Gochi Hand',
		'Gorditas',
		'Gothic A1',
		'Gotu',
		'Goudy Bookletter 1911',
		'Graduate',
		'Grand Hotel',
		'Grandstander',
		'Gravitas One',
		'Great Vibes',
		'Grenze',
		'Grenze Gotisch',
		'Griffy',
		'Gruppo',
		'Gudea',
		'Gugi',
		'Gupter',
		'Gurajada',
		'Habibi',
		'Halant',
		'Hammersmith One',
		'Hanalei',
		'Hanalei Fill',
		'Handlee',
		'Hanuman',
		'Happy Monkey',
		'Harmattan',
		'Headland One',
		'Heebo',
		'Henny Penny',
		'Hepta Slab',
		'Herr Von Muellerhoff',
		'Hi Melody',
		'Hind',
		'Hind Guntur',
		'Hind Madurai',
		'Hind Siliguri',
		'Hind Vadodara',
		'Holtwood One SC',
		'Homemade Apple',
		'Homenaje',
		'IBM Plex Mono',
		'IBM Plex Sans',
		'IBM Plex Sans Condensed',
		'IBM Plex Serif',
		'IM Fell DW Pica',
		'IM Fell DW Pica SC',
		'IM Fell Double Pica',
		'IM Fell Double Pica SC',
		'IM Fell English',
		'IM Fell English SC',
		'IM Fell French Canon',
		'IM Fell French Canon SC',
		'IM Fell Great Primer',
		'IM Fell Great Primer SC',
		'Ibarra Real Nova',
		'Iceberg',
		'Iceland',
		'Imprima',
		'Inconsolata',
		'Inder',
		'Indie Flower',
		'Inika',
		'Inknut Antiqua',
		'Inria Sans',
		'Inria Serif',
		'Inter',
		'Irish Grover',
		'Istok Web',
		'Italiana',
		'Italianno',
		'Itim',
		'Jacques Francois',
		'Jacques Francois Shadow',
		'Jaldi',
		'Jim Nightshade',
		'Jockey One',
		'Jolly Lodger',
		'Jomhuria',
		'Jomolhari',
		'Josefin Sans',
		'Josefin Slab',
		'Jost',
		'Joti One',
		'Jua',
		'Judson',
		'Julee',
		'Julius Sans One',
		'Junge',
		'Jura',
		'Just Another Hand',
		'Just Me Again Down Here',
		'K2D',
		'Kadwa',
		'Kalam',
		'Kameron',
		'Kanit',
		'Kantumruy',
		'Karla',
		'Karma',
		'Katibeh',
		'Kaushan Script',
		'Kavivanar',
		'Kavoon',
		'Kdam Thmor',
		'Keania One',
		'Kelly Slab',
		'Kenia',
		'Khand',
		'Khmer',
		'Khula',
		'Kirang Haerang',
		'Kite One',
		'Knewave',
		'KoHo',
		'Kodchasan',
		'Kosugi',
		'Kosugi Maru',
		'Kotta One',
		'Koulen',
		'Kranky',
		'Kreon',
		'Kristi',
		'Krona One',
		'Krub',
		'Kufam',
		'Kulim Park',
		'Kumar One',
		'Kumar One Outline',
		'Kumbh Sans',
		'Kurale',
		'La Belle Aurore',
		'Lacquer',
		'Laila',
		'Lakki Reddy',
		'Lalezar',
		'Lancelot',
		'Lateef',
		'Lato',
		'League Script',
		'Leckerli One',
		'Ledger',
		'Lekton',
		'Lemon',
		'Lemonada',
		'Lexend Deca',
		'Lexend Exa',
		'Lexend Giga',
		'Lexend Mega',
		'Lexend Peta',
		'Lexend Tera',
		'Lexend Zetta',
		'Libre Barcode 128',
		'Libre Barcode 128 Text',
		'Libre Barcode 39',
		'Libre Barcode 39 Extended',
		'Libre Barcode 39 Extended Text',
		'Libre Barcode 39 Text',
		'Libre Baskerville',
		'Libre Caslon Display',
		'Libre Caslon Text',
		'Libre Franklin',
		'Life Savers',
		'Lilita One',
		'Lily Script One',
		'Limelight',
		'Linden Hill',
		'Literata',
		'Liu Jian Mao Cao',
		'Livvic',
		'Lobster',
		'Lobster Two',
		'Londrina Outline',
		'Londrina Shadow',
		'Londrina Sketch',
		'Londrina Solid',
		'Long Cang',
		'Lora',
		'Love Ya Like A Sister',
		'Loved by the King',
		'Lovers Quarrel',
		'Luckiest Guy',
		'Lusitana',
		'Lustria',
		'M PLUS 1p',
		'M PLUS Rounded 1c',
		'Ma Shan Zheng',
		'Macondo',
		'Macondo Swash Caps',
		'Mada',
		'Magra',
		'Maiden Orange',
		'Maitree',
		'Major Mono Display',
		'Mako',
		'Mali',
		'Mallanna',
		'Mandali',
		'Manjari',
		'Manrope',
		'Mansalva',
		'Manuale',
		'Marcellus',
		'Marcellus SC',
		'Marck Script',
		'Margarine',
		'Markazi Text',
		'Marko One',
		'Marmelad',
		'Martel',
		'Martel Sans',
		'Marvel',
		'Mate',
		'Mate SC',
		'Maven Pro',
		'McLaren',
		'Meddon',
		'MedievalSharp',
		'Medula One',
		'Meera Inimai',
		'Megrim',
		'Meie Script',
		'Merienda',
		'Merienda One',
		'Merriweather',
		'Merriweather Sans',
		'Metal',
		'Metal Mania',
		'Metamorphous',
		'Metrophobic',
		'Michroma',
		'Milonga',
		'Miltonian',
		'Miltonian Tattoo',
		'Mina',
		'Miniver',
		'Miriam Libre',
		'Mirza',
		'Miss Fajardose',
		'Mitr',
		'Modak',
		'Modern Antiqua',
		'Mogra',
		'Molengo',
		'Molle',
		'Monda',
		'Monofett',
		'Monoton',
		'Monsieur La Doulaise',
		'Montaga',
		'Montez',
		'Montserrat',
		'Montserrat Alternates',
		'Montserrat Subrayada',
		'Moul',
		'Moulpali',
		'Mountains of Christmas',
		'Mouse Memoirs',
		'Mr Bedfort',
		'Mr Dafoe',
		'Mr De Haviland',
		'Mrs Saint Delafield',
		'Mrs Sheppards',
		'Mukta',
		'Mukta Mahee',
		'Mukta Malar',
		'Mukta Vaani',
		'Muli',
		'Mulish',
		'MuseoModerno',
		'Mystery Quest',
		'NTR',
		'Nanum Brush Script',
		'Nanum Gothic',
		'Nanum Gothic Coding',
		'Nanum Myeongjo',
		'Nanum Pen Script',
		'Neucha',
		'Neuton',
		'New Rocker',
		'News Cycle',
		'Niconne',
		'Niramit',
		'Nixie One',
		'Nobile',
		'Nokora',
		'Norican',
		'Nosifer',
		'Notable',
		'Nothing You Could Do',
		'Noticia Text',
		'Noto Sans',
		'Noto Sans HK',
		'Noto Sans JP',
		'Noto Sans KR',
		'Noto Sans SC',
		'Noto Sans TC',
		'Noto Serif',
		'Noto Serif JP',
		'Noto Serif KR',
		'Noto Serif SC',
		'Noto Serif TC',
		'Nova Cut',
		'Nova Flat',
		'Nova Mono',
		'Nova Oval',
		'Nova Round',
		'Nova Script',
		'Nova Slim',
		'Nova Square',
		'Numans',
		'Nunito',
		'Nunito Sans',
		'Odibee Sans',
		'Odor Mean Chey',
		'Offside',
		'Old Standard TT',
		'Oldenburg',
		'Oleo Script',
		'Oleo Script Swash Caps',
		'Open Sans',
		'Open Sans Condensed',
		'Oranienbaum',
		'Orbitron',
		'Oregano',
		'Orienta',
		'Original Surfer',
		'Oswald',
		'Over the Rainbow',
		'Overlock',
		'Overlock SC',
		'Overpass',
		'Overpass Mono',
		'Ovo',
		'Oxanium',
		'Oxygen',
		'Oxygen Mono',
		'PT Mono',
		'PT Sans',
		'PT Sans Caption',
		'PT Sans Narrow',
		'PT Serif',
		'PT Serif Caption',
		'Pacifico',
		'Padauk',
		'Palanquin',
		'Palanquin Dark',
		'Pangolin',
		'Paprika',
		'Parisienne',
		'Passero One',
		'Passion One',
		'Pathway Gothic One',
		'Patrick Hand',
		'Patrick Hand SC',
		'Pattaya',
		'Patua One',
		'Pavanam',
		'Paytone One',
		'Peddana',
		'Peralta',
		'Permanent Marker',
		'Petit Formal Script',
		'Petrona',
		'Philosopher',
		'Piedra',
		'Pinyon Script',
		'Pirata One',
		'Plaster',
		'Play',
		'Playball',
		'Playfair Display',
		'Playfair Display SC',
		'Podkova',
		'Poiret One',
		'Poller One',
		'Poly',
		'Pompiere',
		'Pontano Sans',
		'Poor Story',
		'Poppins',
		'Port Lligat Sans',
		'Port Lligat Slab',
		'Pragati Narrow',
		'Prata',
		'Preahvihear',
		'Press Start 2P',
		'Pridi',
		'Princess Sofia',
		'Prociono',
		'Prompt',
		'Prosto One',
		'Proza Libre',
		'Public Sans',
		'Puritan',
		'Purple Purse',
		'Quando',
		'Quantico',
		'Quattrocento',
		'Quattrocento Sans',
		'Questrial',
		'Quicksand',
		'Quintessential',
		'Qwigley',
		'Racing Sans One',
		'Radley',
		'Rajdhani',
		'Rakkas',
		'Raleway',
		'Raleway Dots',
		'Ramabhadra',
		'Ramaraja',
		'Rambla',
		'Rammetto One',
		'Ranchers',
		'Rancho',
		'Ranga',
		'Rasa',
		'Rationale',
		'Ravi Prakash',
		'Recursive',
		'Red Hat Display',
		'Red Hat Text',
		'Red Rose',
		'Redressed',
		'Reem Kufi',
		'Reenie Beanie',
		'Revalia',
		'Rhodium Libre',
		'Ribeye',
		'Ribeye Marrow',
		'Righteous',
		'Risque',
		'Roboto',
		'Roboto Condensed',
		'Roboto Mono',
		'Roboto Slab',
		'Rochester',
		'Rock Salt',
		'Rokkitt',
		'Romanesco',
		'Ropa Sans',
		'Rosario',
		'Rosarivo',
		'Rouge Script',
		'Rowdies',
		'Rozha One',
		'Rubik',
		'Rubik Mono One',
		'Ruda',
		'Rufina',
		'Ruge Boogie',
		'Ruluko',
		'Rum Raisin',
		'Ruslan Display',
		'Russo One',
		'Ruthie',
		'Rye',
		'Sacramento',
		'Sahitya',
		'Sail',
		'Saira',
		'Saira Condensed',
		'Saira Extra Condensed',
		'Saira Semi Condensed',
		'Saira Stencil One',
		'Salsa',
		'Sanchez',
		'Sancreek',
		'Sansita',
		'Sarabun',
		'Sarala',
		'Sarina',
		'Sarpanch',
		'Satisfy',
		'Sawarabi Gothic',
		'Sawarabi Mincho',
		'Scada',
		'Scheherazade',
		'Schoolbell',
		'Scope One',
		'Seaweed Script',
		'Secular One',
		'Sedgwick Ave',
		'Sedgwick Ave Display',
		'Sen',
		'Sevillana',
		'Seymour One',
		'Shadows Into Light',
		'Shadows Into Light Two',
		'Shanti',
		'Share',
		'Share Tech',
		'Share Tech Mono',
		'Shojumaru',
		'Short Stack',
		'Shrikhand',
		'Siemreap',
		'Sigmar One',
		'Signika',
		'Signika Negative',
		'Simonetta',
		'Single Day',
		'Sintony',
		'Sirin Stencil',
		'Six Caps',
		'Skranji',
		'Slabo 13px',
		'Slabo 27px',
		'Slackey',
		'Smokum',
		'Smythe',
		'Sniglet',
		'Snippet',
		'Snowburst One',
		'Sofadi One',
		'Sofia',
		'Solway',
		'Song Myung',
		'Sonsie One',
		'Sora',
		'Sorts Mill Goudy',
		'Source Code Pro',
		'Source Sans Pro',
		'Source Serif Pro',
		'Space Mono',
		'Spartan',
		'Special Elite',
		'Spectral',
		'Spectral SC',
		'Spicy Rice',
		'Spinnaker',
		'Spirax',
		'Squada One',
		'Sree Krushnadevaraya',
		'Sriracha',
		'Srisakdi',
		'Staatliches',
		'Stalemate',
		'Stalinist One',
		'Stardos Stencil',
		'Stint Ultra Condensed',
		'Stint Ultra Expanded',
		'Stoke',
		'Strait',
		'Stylish',
		'Sue Ellen Francisco',
		'Suez One',
		'Sulphur Point',
		'Sumana',
		'Sunflower',
		'Sunshiney',
		'Supermercado One',
		'Sura',
		'Suranna',
		'Suravaram',
		'Suwannaphum',
		'Swanky and Moo Moo',
		'Syncopate',
		'Syne',
		'Tajawal',
		'Tangerine',
		'Taprom',
		'Tauri',
		'Taviraj',
		'Teko',
		'Telex',
		'Tenali Ramakrishna',
		'Tenor Sans',
		'Text Me One',
		'Thasadith',
		'The Girl Next Door',
		'Tienne',
		'Tillana',
		'Timmana',
		'Tinos',
		'Titan One',
		'Titillium Web',
		'Tomorrow',
		'Trade Winds',
		'Trirong',
		'Trocchi',
		'Trochut',
		'Trykker',
		'Tulpen One',
		'Turret Road',
		'Ubuntu',
		'Ubuntu Condensed',
		'Ubuntu Mono',
		'Ultra',
		'Uncial Antiqua',
		'Underdog',
		'Unica One',
		'UnifrakturCook',
		'UnifrakturMaguntia',
		'Unkempt',
		'Unlock',
		'Unna',
		'VT323',
		'Vampiro One',
		'Varela',
		'Varela Round',
		'Varta',
		'Vast Shadow',
		'Vesper Libre',
		'Viaoda Libre',
		'Vibes',
		'Vibur',
		'Vidaloka',
		'Viga',
		'Voces',
		'Volkhov',
		'Vollkorn',
		'Vollkorn SC',
		'Voltaire',
		'Waiting for the Sunrise',
		'Wallpoet',
		'Walter Turncoat',
		'Warnes',
		'Wellfleet',
		'Wendy One',
		'Wire One',
		'Work Sans',
		'Yanone Kaffeesatz',
		'Yantramanav',
		'Yatra One',
		'Yellowtail',
		'Yeon Sung',
		'Yeseva One',
		'Yesteryear',
		'Yrsa',
		'ZCOOL KuaiLe',
		'ZCOOL QingKe HuangYou',
		'ZCOOL XiaoWei',
		'Zeyada',
		'Zhi Mang Xing',
		'Zilla Slab',
		'Zilla Slab Highlight',
	];
}
