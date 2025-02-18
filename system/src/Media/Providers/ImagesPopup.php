<?php

declare(strict_types=1);

namespace Johncms\Media\Providers;

use DiDom\Document;
use Exception;
use Simba77\EmbedMedia\EmbedProvider;

class ImagesPopup implements EmbedProvider
{
    /**
     * @inheritDoc
     * @psalm-suppress MixedOperand
     */
    public function parse(string $content): string
    {
        try {
            $document = new Document($content);
            $images = $document->find('figure.image');
            foreach ($images as $image) {
                $html = $image->innerHtml();
                $img = $image->first('img');
                $image->setInnerHtml(
                    '<a class="image-preview"
                data-source="' . $img->attr('src') . '"
                title="' . $img->attr('alt') . '"
                href="' . $img->attr('src') . '">' . $html . '</a>'
                );
            }
            return $document->html();
        } catch (Exception $exception) {
        }
        return $content;
    }
}
