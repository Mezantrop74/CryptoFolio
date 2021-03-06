<?php

declare(strict_types=1);

namespace App\Domain\Telegram\Parser;


use Carbon\Carbon;

class Message
{
    /**
     * @var int
     */
    public int $id;

    /**
     * @var User
     */
    public User $user;

    /**
     * @var string|null
     */
    public ?string $text = null;

    /**
     * @var \DateTime
     */
    public \DateTime $publication_date;

    /**
     * @var int
     */
    public int $views_number;

    /**
     * @var array|null
     */
    public ?array $files;

    /**
     * @param string $message_html
     * @return static
     */
    public static function createByHtml(string $message_html): self
    {
        $result = new self();
        /** tgme_widget_message js-widget_message */
        if(!preg_match('|<div class="tgme_widget_message js-widget_message" data-post=".*?/(\d+)"|sui', $message_html, $id_match)){
            throw new \Error('Message ID not found');
        }
        $id = (int)$id_match[1];
        $result->id = $id;

        if(preg_match('|tgme_widget_message_text js-message_text" dir="auto">(.*?)</div>|sui', $message_html, $text_match)){
            $result->text = strip_tags(html_entity_decode($text_match[1]));
        }

        if(preg_match('|<div class="link_preview_description" dir="auto"><i class=".*?">(.*?)</div>\s*</a>|sui', $message_html, $preview_match)){
            $result->text = strip_tags(html_entity_decode($preview_match[1]));
        }

        if(!preg_match('|<time datetime="(.*?)T(.*?)\+00:00"|sui', $message_html, $time_match)){
            throw new \Error('Time information not found');
        }

        $date = Carbon::parse("{$time_match[1]} {$time_match[2]}");
        $result->publication_date = $date;

        if(!preg_match('|<span class="tgme_widget_message_views">(.*?)</span>|sui', $message_html, $views_match)){
            throw new \Error('Views amount not found');
        }

        if(preg_match('|[.K]|sui', $views_match[1], $view_data))
        {
            $views = explode('.', $views_match[1]);
            $result->views_number = (int)$views[0] * 1000 + (int)$views[1] * 100;
        }else{
            $result->views_number = (int)$views_match[1];
        }

        $result->files = [];

        if(preg_match('|<video src="(.*?)" class="tgme_widget_message_video js-message_video"' .
            ' width=".*?" height=".*?"></video>|sui', $message_html, $video_match)){
            $video = new File($video_match[1]);
            array_push($result->files, $video);
        }

        if(preg_match_all('|<a class="tgme_widget_message_photo_wrap grouped_media_wrap blured js-message_photo" ' .
            'style=".*?background-image:url\(\'(.*?)\'\)".*?\s*<div class="grouped_media_helper" style=".*?">\s*' .
            '<div class="tgme_widget_message_photo grouped_media" style=".*?"></div>|sui', $message_html, $photo_grouped_match)){
            foreach ($photo_grouped_match[1] as $photo){
            $photo_parsed = new File($photo);
            array_push($result->files, $photo_parsed);
            }
        }



        return $result;
    }
}
