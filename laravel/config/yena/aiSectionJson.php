<?php

return [
  '{
    "chat": {
      "response": "Ai chat response"
    },
    "sections": [
    {
      "banner": {
        "content": {
          "title": "Title of the section",
          "subtitle": "Add a brief description of this section"
        },
        "settings": {
          "banner_style": "choose between 1-6",
          "enable_image": true,
          "actiontype": "button",
          "align": "left",
          "height": "320",
          "width": "75",
          "title": "s",
          "image_type": "fill",
          "enable_action": true,
          "button_one_text": "Button Text"
        },
        "form": {
          "email": "Email",
          "button_name": "Signup"
        }
      },
      "gallery": {
        "content": {
          "title": "Heading",
          "subtitle": "Add a brief description of this section"
        },
        "settings": {
          "desktop_grid": 4,
          "mobile_grid": 3,
          "desktop_height": 250,
          "mobile_height": 250
        },
        "items": [
          {
            "content": {
              "image": null
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "testimonial": {
        "content": {
          "title": "Heading"
        },
        "settings": {
          "style": "1",
          "align": "left",
          "desktop_grid": 3,
          "mobile_grid": 1,
          "text": "s",
          "background": true,
          "rating": true,
          "avatar": true,
          "type": "stars",
          "shape": "square"
        },
        "form": [],
        "items": [
          {
            "content": {
              "title": "Testimonial item person name",
              "bio": "Testimonial item person bio",
              "text": "Testimonial review text",
              "rating": 2
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "pricing": {
        "content": {
          "title": "Heading"
        },
        "settings": {
          "style": "1",
          "align": "left",
          "layout": "left",
          "display": "grid",
          "desktop_grid": 3,
          "mobile_grid": 1,
          "text": "m",
          "background": true,
          "icon": true,
          "type": "plans",
          "shape": "square",
          "desktop_height": "50",
          "currency": "USD"
        },
        "form": [],
        "items": [
          {
            "content": {
              "title": "Pricing header name",
              "button": "Pricing button or use ",
              "single_price": "0",
              "month_price": "0",
              "year_price": "0",
              "features": [
                {
                  "name": "Feature 1"
                },
                {
                  "name": "Feature 2"
                },
                {
                  "name": "Feature 3"
                }
              ]
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "logos": {
        "content": {
          "title": "Heading",
          "subtitle": "Add a brief description of this section"
        },
        "settings": {
          "align": "left",
          "display": "grid",
          "desktop_grid": 4,
          "mobile_grid": 3,
          "desktop_height": 50,
          "mobile_height": 100,
          "desktop_width": 100,
          "mobile_width": 200,
          "background": true
        },
        "form": [],
        "items": [
          {
            "content": {
              "link": "",
              "desktop_size": 1,
              "mobile_size": 1
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "list": {
        "content": {
          "title": "Heading"
        },
        "settings": {
          "style": "1",
          "align": "left",
          "layout": "left",
          "display": "grid",
          "desktop_grid": 3,
          "mobile_grid": 1,
          "text": "m",
          "background": true,
          "icon": true,
          "type": "stars",
          "shape": "square",
          "desktop_height": "50"
        },
        "form": [],
        "items": [
          {
            "content": {
              "title": "List 1"
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "accordion": {
        "content": {
          "title": "Heading",
          "subtitle": "Add a brief description of this section"
        },
        "settings": {
          "banner_style": 1
        },
        "form": [],
        "items": [
          {
            "content": {
              "title": "Add title",
              "text": "Add description"
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "card": {
        "content": {
          "title": "Title of card section"
        },
        "settings": {
          "style": "1",
          "align": "left",
          "layout_align": "bottom",
          "desktop_grid": 3,
          "mobile_grid": 1,
          "desktop_height": 250,
          "mobile_height": 250,
          "text": "s",
          "background": true,
          "enable_image": true
        },
        "items": [
          {
            "content": {
              "title": "Title of card item",
              "text": "Short text of card item",
              "button": "",
              "color": "accent"
            },
            "settings": {
              "animation": "-"
            }
          }
        ]
      },
      "text": {
        "content": {
          "title": "Title of the text section",
          "subtitle": "Markdown description of this section"
        }
      }
    }
  ]
  }'
];