<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BioSiteComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'bio_site_id',
        'component_type',
        'component_props',
        'position',
        'is_visible'
    ];

    protected $casts = [
        'component_props' => 'array',
        'is_visible' => 'boolean'
    ];

    /**
     * Get the bio site that owns the component
     */
    public function bioSite(): BelongsTo
    {
        return $this->belongsTo(BioSite::class);
    }

    /**
     * Scope for visible components
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope for ordered components
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Get component with default props merged
     */
    public function getComponentWithDefaults()
    {
        $defaults = $this->getDefaultProps();
        $props = array_merge($defaults, $this->component_props ?? []);
        
        return [
            'id' => $this->id,
            'type' => $this->component_type,
            'props' => $props,
            'position' => $this->position,
            'visible' => $this->is_visible
        ];
    }

    /**
     * Get default props for component type
     */
    private function getDefaultProps()
    {
        $defaults = [
            'text' => [
                'content' => 'Your text here',
                'font_size' => 16,
                'font_weight' => 'normal',
                'color' => '#000000',
                'align' => 'left',
                'margin' => '10px 0'
            ],
            'link' => [
                'title' => 'Link Title',
                'url' => 'https://example.com',
                'background_color' => '#007bff',
                'text_color' => '#ffffff',
                'border_radius' => '8px',
                'padding' => '12px 24px',
                'margin' => '8px 0',
                'target' => '_blank'
            ],
            'image' => [
                'src' => 'https://via.placeholder.com/300x200',
                'alt' => 'Image description',
                'width' => '100%',
                'height' => 'auto',
                'border_radius' => '8px',
                'margin' => '10px 0'
            ],
            'social_links' => [
                'links' => [],
                'size' => 'medium',
                'style' => 'rounded',
                'align' => 'center',
                'margin' => '15px 0'
            ],
            'contact_form' => [
                'title' => 'Contact Me',
                'fields' => [
                    ['type' => 'text', 'name' => 'name', 'placeholder' => 'Your Name', 'required' => true],
                    ['type' => 'email', 'name' => 'email', 'placeholder' => 'Your Email', 'required' => true],
                    ['type' => 'textarea', 'name' => 'message', 'placeholder' => 'Your Message', 'required' => true]
                ],
                'button_text' => 'Send Message',
                'button_color' => '#28a745',
                'margin' => '20px 0'
            ]
        ];

        return $defaults[$this->component_type] ?? [];
    }

    /**
     * Update component position
     */
    public function updatePosition($newPosition)
    {
        $this->update(['position' => $newPosition]);
    }

    /**
     * Toggle component visibility
     */
    public function toggleVisibility()
    {
        $this->update(['is_visible' => !$this->is_visible]);
    }

    /**
     * Duplicate component
     */
    public function duplicate()
    {
        $newComponent = $this->replicate();
        $newComponent->position = $this->bioSite->components()->max('position') + 1;
        $newComponent->save();
        
        return $newComponent;
    }

    /**
     * Get component HTML for rendering
     */
    public function renderHtml()
    {
        $props = $this->component_props ?? [];
        
        switch ($this->component_type) {
            case 'text':
                return $this->renderTextComponent($props);
            case 'link':
                return $this->renderLinkComponent($props);
            case 'image':
                return $this->renderImageComponent($props);
            case 'social_links':
                return $this->renderSocialLinksComponent($props);
            case 'contact_form':
                return $this->renderContactFormComponent($props);
            default:
                return '<div>Unknown component type</div>';
        }
    }

    /**
     * Render text component
     */
    private function renderTextComponent($props)
    {
        $content = $props['content'] ?? 'Your text here';
        $fontSize = $props['font_size'] ?? 16;
        $fontWeight = $props['font_weight'] ?? 'normal';
        $color = $props['color'] ?? '#000000';
        $align = $props['align'] ?? 'left';
        $margin = $props['margin'] ?? '10px 0';

        return "<div style='font-size: {$fontSize}px; font-weight: {$fontWeight}; color: {$color}; text-align: {$align}; margin: {$margin};'>{$content}</div>";
    }

    /**
     * Render link component
     */
    private function renderLinkComponent($props)
    {
        $title = $props['title'] ?? 'Link Title';
        $url = $props['url'] ?? '#';
        $backgroundColor = $props['background_color'] ?? '#007bff';
        $textColor = $props['text_color'] ?? '#ffffff';
        $borderRadius = $props['border_radius'] ?? '8px';
        $padding = $props['padding'] ?? '12px 24px';
        $margin = $props['margin'] ?? '8px 0';
        $target = $props['target'] ?? '_blank';

        return "<a href='{$url}' target='{$target}' style='display: block; background-color: {$backgroundColor}; color: {$textColor}; text-decoration: none; border-radius: {$borderRadius}; padding: {$padding}; margin: {$margin}; text-align: center;'>{$title}</a>";
    }

    /**
     * Render image component
     */
    private function renderImageComponent($props)
    {
        $src = $props['src'] ?? 'https://via.placeholder.com/300x200';
        $alt = $props['alt'] ?? 'Image';
        $width = $props['width'] ?? '100%';
        $height = $props['height'] ?? 'auto';
        $borderRadius = $props['border_radius'] ?? '8px';
        $margin = $props['margin'] ?? '10px 0';

        return "<img src='{$src}' alt='{$alt}' style='width: {$width}; height: {$height}; border-radius: {$borderRadius}; margin: {$margin}; display: block;' />";
    }

    /**
     * Render social links component
     */
    private function renderSocialLinksComponent($props)
    {
        $links = $props['links'] ?? [];
        $size = $props['size'] ?? 'medium';
        $align = $props['align'] ?? 'center';
        $margin = $props['margin'] ?? '15px 0';

        $iconSize = $size === 'small' ? '24px' : ($size === 'large' ? '40px' : '32px');
        
        $html = "<div style='text-align: {$align}; margin: {$margin};'>";
        foreach ($links as $link) {
            $platform = $link['platform'] ?? '';
            $url = $link['url'] ?? '#';
            $html .= "<a href='{$url}' target='_blank' style='display: inline-block; margin: 0 8px;'>";
            $html .= "<div style='width: {$iconSize}; height: {$iconSize}; background-color: #333; border-radius: 50%; display: inline-block;'></div>";
            $html .= "</a>";
        }
        $html .= "</div>";

        return $html;
    }

    /**
     * Render contact form component
     */
    private function renderContactFormComponent($props)
    {
        $title = $props['title'] ?? 'Contact Me';
        $buttonText = $props['button_text'] ?? 'Send Message';
        $buttonColor = $props['button_color'] ?? '#28a745';
        $margin = $props['margin'] ?? '20px 0';

        return "<div style='margin: {$margin};'><h3>{$title}</h3><form><input type='text' placeholder='Your Name' style='width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px;' /><input type='email' placeholder='Your Email' style='width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px;' /><textarea placeholder='Your Message' style='width: 100%; padding: 10px; margin: 5px 0; border: 1px solid #ccc; border-radius: 4px; height: 100px;'></textarea><button type='submit' style='background-color: {$buttonColor}; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>{$buttonText}</button></form></div>";
    }
}