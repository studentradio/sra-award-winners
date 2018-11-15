<?php
/**
 * Created by PhpStorm.
 * User: fredbradley
 * Date: 15/11/2018
 * Time: 12:48
 */

namespace StudentRadio\AwardWinners;

class SetPermalinks
{
    public function __construct()
    {
        add_filter('register_post_type_args', [$this, 'filter_cpt'], 11, 2);
        add_filter('register_taxonomy_args', [$this, 'filter_tax'], 11, 3);
        add_filter('post_type_link', [$this, 'show_permalink'], 1, 2);
    }

    public function show_permalink($post_link, $post)
    {
        if (is_object($post) && $post->post_type == 'sra-award-winner') {
            $terms = wp_get_object_terms($post->ID, 'award-year');
            if ($terms) {
                return str_replace('%award_year%', $terms[0]->slug, $post_link);
            }
        }

        return $post_link;
    }

    public function filter_tax($args, $taxonomy, $object_type)
    {
        if ($taxonomy !== 'award-year') {
            return $args;
        }

        $tax_args = [
            "rewrite" => [
                'slug' => 'winners',
                'with_front' => false,
            ],
        ];

        return array_merge($args, $tax_args);
    }

    public function filter_cpt($args, $post_type)
    {
        if ($post_type !== 'sra-award-winner') {
            return $args;
        }

        $cpt_args = [
            'rewrite' => [
                'slug' => 'winners/%award_year%',
                'with_front' => false,
            ],
            'has_archive' => 'winners',
        ];

        return array_merge($args, $cpt_args);
    }
}
