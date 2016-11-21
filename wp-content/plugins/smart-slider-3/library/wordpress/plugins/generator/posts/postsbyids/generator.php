<?php

N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2GeneratorPostsPostsByIDs extends N2GeneratorAbstract
{

    protected function _getData($count, $startIndex) {
        global $post, $wp_the_query;
        $tmpPost         = $post;
        $tmpWp_the_query = $wp_the_query;
        $wp_the_query = null;

        $i    = 0;
        $data = array();

        foreach ($this->getIDs() AS $id) {
            $record = array();
            $post   = get_post($id);
            setup_postdata($post);

            $record['id'] = $post->ID;


            $record['url']         = get_permalink();
            $record['title']       = apply_filters('the_title', get_the_title());
            $record['description'] = $record['content'] = get_the_content();
            $record['author_name'] = $record['author'] = get_the_author();
            $record['author_url']  = get_the_author_meta('url');
            $record['date']        = get_the_date();
            $record['excerpt']     = get_the_excerpt();
            $record['modified']    = get_the_modified_date();

            $category = get_the_category($post->ID);
            if (isset($category[0])) {
                $record['category_name'] = $category[0]->name;
                $record['category_link'] = get_category_link($category[0]->cat_ID);
            } else {
                $record['category_name'] = '';
                $record['category_link'] = '';
            }

            $record['featured_image'] = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
            if (!$record['featured_image']) {
                $record['featured_image'] = '';
            } else {
                $thumbnail_meta = get_post_meta($thumbnail_id, '_wp_attachment_metadata', true);
                if (isset($thumbnail_meta['sizes'])) {
                    $sizes = $this->getImageSizes($thumbnail_id, $thumbnail_meta['sizes']);
                    $record = array_merge($record, $sizes);
                }
            }

            $record['thumbnail'] = $record['image'] = $record['featured_image'];
            $record['url_label'] = 'View post';

            if (class_exists('acf')) {
                $fields = get_fields($post->ID);
                if (count($fields) && is_array($fields) && !empty($fields)) {
                    foreach ($fields AS $k => $v) {
                        $type   = $this->getACFType($k,$post->ID);
                        $k      = str_replace('-', '', $k);

                        while (isset($record[$k])) {
                            $k  = 'acf_' . $k;
                        };


                        if (!is_array($v) && !is_object($v)) {
                            if($type['type'] == "image" && is_numeric($type["value"])){
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $src = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                $v = $src[0];
                            }
                            $record[$k] = $v;
                        } else if (!is_object($v) && isset($v['url'])) {
                            $record[$k] = $v['url'];
                        }
                        if($type['type'] == "image" && (is_numeric($type["value"]) || is_array($type['value']))){
                            if(is_array($type['value'])){
                                $sizes              = $this->getImageSizes($type["value"]["id"], $type["value"]["sizes"], $k);
                            } else {
                                $thumbnail_meta     = wp_get_attachment_metadata($type["value"]);
                                $sizes              = $this->getImageSizes($type["value"], $thumbnail_meta['sizes'], $k);
                            }
                            $record = array_merge($record, $sizes);
                        }
                    }
                }
            }
            
            $data[$i] = &$record;
            unset($record);
            $i++;
        }

        $wp_the_query = $tmpWp_the_query;

        wp_reset_postdata();
        $post = $tmpPost;
        if ($post) setup_postdata($post);

        return $data;
    }

    protected function getImageSizes($thumbnail_id, $sizes, $prefix = false) {
        $data = array();
        if(!$prefix){
            $prefix = "";
        } else {
            $prefix = $prefix . "_";
        }
        foreach ($sizes AS $size => $image) {
            $imageSrc               = wp_get_attachment_image_src($thumbnail_id, $size);
            $data[$prefix.'image_' . $size] = $imageSrc[0];
        }
        return $data;
    }

    protected function getACFType($key, $post_id) {
        $type = get_field_object($key, $post_id);
        return $type;
    }
}