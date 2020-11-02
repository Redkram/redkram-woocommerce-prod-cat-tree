<?php

	/**
	 * Plugin Name: REDKRAM - Product Category Tree
	 * Plugin URI: https://www.office24.net
	 * Description: Product Category Tree for Woocommerce
	 * Author: Redkram
	 * Author URI: https://github.com/Redkram/
	 * Version: 0.0.1
	 * Text Domain: ProdCatTree
	 * License: GPLv2 or later
	 */

	/*
	 * This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

	*/

	class ProdCatTree
	{

		private $name;
		private $loc;

		public function __construct() {
			$this->name = 'ProdCatTree';
			$this->loc  = 'primary';
			self::wpb_custom_new_menu();
		}

		public function activate () {
			flush_rewrite_rules();
			self::wpb_custom_new_menu();
		}

		public function deactivate () {
			self::deleteMenu("El plugin ha sido desactivado");
		}

		public function uninstall () {
			self::deleteMenu("El plugin ha sido eliminado");
		}

		public function deleteMenu ($msg)
		{
			global $wpdb;
			$wpdb->query("DELETE wt,wtt,wtr,wp,wpm
			FROM
			wp_terms wt
			LEFT JOIN 
			wp_term_taxonomy wtt ON wtt.term_id = wt.term_id
			LEFT JOIN 
			wp_term_relationships wtr ON wtr.term_taxonomy_id = wtt.term_taxonomy_id
			LEFT JOIN 
			wp_posts wp ON wp.ID = wtr.object_id
			LEFT JOIN 
			wp_postmeta wpm ON wpm.post_id = wp.ID
			WHERE wt.slug = 'prodcattree'");
			flush_rewrite_rules();
			echo $msg;
		}

		public static function my_wp_nav_menu_args ($args = '')
		{
			if( $args['theme_location'] == 'primary' ){
				$args['depth'] = '8';
			}
			return $args;
		}

		private function wpb_custom_new_menu()
		{
			add_filter( 'wp_nav_menu_args', [__CLASS__, 'my_wp_nav_menu_args'] );
			$menu = wp_get_nav_menu_object( $this->name );
			if( !$menu ) {
				$menu_id = wp_update_nav_menu_object( 0, ['menu-name' => $this->name ] );
				wp_update_nav_menu_item(
					$menu_id, 0, [
						'menu-item-title' => __("Inicio"),
						'menu-item-classes' => "home",
						'menu-item-url' => home_url( '/' ),
						'menu-item-status' => 'publish'
					]
				);
				$parent = wp_update_nav_menu_item(
					$menu_id, 0, [
						'menu-item-title' => __("Tienda Online"),
						'menu-item-classes' => "online",
						'menu-item-url' => home_url( '/tienda/' ),
						'menu-item-status' => 'publish'
					]
				);
				self::rkGetCats($menu_id, $parent);
				wp_update_nav_menu_item(
					$menu_id, 0, [
						'menu-item-title' => __("Contacto"),
						'menu-item-classes' => "contact",
						'menu-item-url' => home_url( '/contacto/' ),
						'menu-item-status' => 'publish'
					]
				);
				if (!has_nav_menu($this->loc)) {
					$locations = get_theme_mod('nav_menu_locations');
					$locations[$this->loc] = $menu_id;
					set_theme_mod('nav_menu_locations', $locations);
				}
			}
		}
		public function rkGetCats(int $menu_id, int $parent) {
			$args = array(
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'show_count'   => false,
				'hierarchical' => true,
				'title_li'     => '',
				'hide_empty'   => false,
				'parent'       => 0,
				'fields' => 'all'
			);
			$all_categories = get_categories( $args );
			foreach ($all_categories as $category) {
				if ($category->slug == "sin-categorizar") continue;
				self::rkGetChilds($args, $category, $menu_id, $parent);
			}
		}

		public function rkGetChilds(array $args, object $category, int $menu_id, int $parent) {
			$id_menu = wp_update_nav_menu_item(
				$menu_id, 0,
				[
					'menu-item-title' => $category->name,
					'menu-item-classes' => $category->slug,
					'menu-item-url' => get_term_link($category, 'product_cat' ),
					'menu-item-status' => 'publish',
					'menu-item-parent-id' => $parent,
				]
			);
			$args['parent'] = $category->term_id;
			$sub_cats = get_categories($args);
			if($sub_cats) {
				foreach($sub_cats as $sub_category) {
					if ($sub_category->term_id != 11356 && $sub_category->term_id != 11368)
						self::rkGetChilds($args, $sub_category, $menu_id, $id_menu);
				}
			}
		}
	}
	if (class_exists('ProdCatTree')) {
		$ProdCatTree = new ProdCatTree();
	}
	if ( defined( ' ABSPATH') ) {
		// activate
		register_activation_hook(__FILE__, [$ProdCatTree, 'activate']);
		// deactivate
		register_deactivation_hook(__FILE__, [$ProdCatTree, 'deactivate']);
		// uninstall
		register_uninstall_hook(__FILE__, [$ProdCatTree, 'uninstall']);
	}