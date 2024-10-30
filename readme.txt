=== Builderius Website Builder – Next Generation Page Builder ===
Contributors: builderius, mrpsiho, vdenchyk, thisbit
Tags: Page Builder, Theme Builder, GraphQL, Code, Live Editor
Requires at least: 5.4
Tested up to: 6.6
Stable tag: 0.15
Requires PHP: 7.3
License: GPL v2.0 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

Builderius is a professional website builder and a page builder for WordPress. Builderius has unparalleled granular control over every aspect of HTML, CSS, and Dynamic data.

== Description ==

= Builderius 1.0 Alpha Information =

**Current stable Builderius version (0.15) is in maintenance mode**. We keep it updated, tested and secure, but we are in the process of redevelopment and redesign of the entire product. Builderius 1.0 Alpha of the upcoming builder has been released in July 2024. You can  download your copy on [https://builderius.io/alpha/](https://builderius.io/alpha/) until we reach stable version at which point we will update the version available here. Please don't use it on production websites.

= Compatibility Notice! =

**New Builderius 1.0 free version is NOT COMPATIBLE with the old Pro version**. DO NOT update your existing website built with the old version of pro, until we have released both Free and the Pro 1.0 stable versions. New versions are a complete overwrite and mixing New Free and old Pro versions will not work.

= Builderius 1.0 Alpha has been released (YouTube Video) =

https://www.youtube.com/watch?v=-ecvt6xrqCA

== About the plugin ==

Builderius is a professional website builder and a page builder for WordPress. Builderius has unparalleled granular control over every aspect of HTML, CSS, and Dynamic data. Developed and designed by a seasoned team, Builderius brings cutting-edge web development practices and proven and efficient workflows that will allow you to build websites that perform, scale, and do not keep you up at night.

Builderius delivers high-performance, accessible, and stable website builds that you will be able to build in a fraction of the time compared to coding, and will not be able to deliver in a reasonable way, using most other website builders in the market.

When you hit the limits of your current builder, try Builderius and enjoy the flexibility and precision it provides.

== Select Builderius Features ==
* **Built-in staging and production branches**: When you save your work, it is saved to the development branch. This means that even when you work on a live website with a lot of traffic, nobody will see your work until you publish a release. No migrations, no worries of new content being overridden. Just push a button and your work is live. Push another button and the previous release is restored.
* **Global Components with Properties**: From Site Header and Site Footer to Button or Card, you can save these as reusable components with properties for local overrides. You can access any component from any template or a page in the builder. No need to leave the builder to open a Header template or Sidebar template, just right-click and choose "edit component," and it will open inside another tab in the same builder interface. The free version supports the header and footer components; for more, get the Pro version.
* **Canvas with tabs**: Work on multiple templates or pages at the same time. Open all of them in tabs, and easily switch between them. No reload, no magic areas.
* **CSS Selectors Panel**: Have access to all selectors used globally or inside the current template in one click. Not only CSS classes, but any valid CSS selector, without the need to use code blocks.
* **CSS Variables Panel**: Have access to all CSS Variables used globally or inside the current template in one click. Manage and organize the variables for consistent and efficient design workflow.
* **Built-in Minimal CSS Framework**: Builderius comes equipped with a CSS Framework for a faster start. The framework is built to be easily extended and modified.
* **CSS Class based workflow**: Easily add and edit classes on any element. No hidden divs, class is added to the element you selected.
* **Responsive design tools**: Add custom breakpoints and select desktop-first or mobile-first as your preferred methodology.
* **Builderius works with JSON**: Builderius's primary data format is JSON, and Builderius elements can design JSON data natively. Builderius is data agnostic, and as long as the format is JSON, it can be designed regardless of where it comes from, WordPress or elsewhere. (PRO)

== Free / Pro Features Distinction ==
Builderius Free is a fully functional page builder. It allows you to build custom global Header and Footer components. It allows you to design your posts, pages, and templates, so it is both a fully functional page builder and site builder. It allows you to create post loops using the WordPress main query, which means you can create Blog Index Page as well as Category and Tags Archive pages, Search results page, and 404 page.

The Pro version brings much more advanced functionality, including multilingual tools, unprecedented dynamic data capabilities, WordPress, and remote. You can create custom components without limitations. And get access to a longer list of useful elements.

Yet, Builderius builder will allow you to build a fully functional website for free, and without hidden gotchas.

== Frequently Asked Questions ==
= Do I need to know how to code to use Builderius? =
No, Builderius is a visual website builder, which means it provides visual tools to create websites. It uses established naming conventions that will be immediately understandable to anyone who knows how to code. This was made so that it aligns with the mental models of the novice user as well as a coder.

= Does Builderius disable a theme? =
On its own, Builderius does not disable anything. It gives you all the tools you need to pick and choose what exactly will be disabled and where. If you create a template for all single posts, then whenever you open a single post, this is where the theme will be completely disabled. You can choose to create a template and assign it to multiple locations, archives, single posts … and the rest of the theme. This will disable the theme completely in a single step. If this template is then set to a higher number of priority, all other templates will override it by default. And that initial template will serve as a fallback.

= What are recommended themes for Builderius? =
It depends on how you intend to use it. In general, Builderius is compatible with any WordPress theme that follows WordPress coding standards. We find it more convenient to work with classic themes simply because they make it easier to set up the links for the menu. However, Builderius works with FSE themes as well. If you do not intend to use the theme for the styling purposes, we recommend a minimal theme: Intentionally Blank. If you want to use advanced hook-related features (PRO only), we have built a starter theme for that. If you intend to use some of the popular themes for general theming but use Builderius (PRO only) to develop only certain features and sections, we recommend [GeneratePress](https://generatepress.com/), [Kadence](https://www.kadencewp.com/), [Blocksy](https://creativethemes.com/blocksy/), or [Astra](https://wpastra.com/). However, any well-coded theme will do.

= Is Builderius a Gutenberg Block Builder? =
No, Builderius works in its own environment, also coded in React JS, focused on precision work and professional development practices. Gutenberg is a mix of a content editor and a site editor, which turns out to limit its capabilities as a development tool. At the current moment, the way Builderius and blocks integrate is that block content is used within Builderius templates and pages as the_content. Alternatively, Builderius can be used to modify individual blocks using the render_blocks hook (PRO only). We have more plans for integrating with the blocks environment in the future.

= Can I use Builderius with the Advanced Custom Fields Plugin? =

Certainly, the pro version works with all ACF fields. The free version of the builder works with simple fields only, including:

* text
* textarea
* number
* range
* email
* url
* password
* image
* file
* wysiwyg editor
* select
* checkbox
* radio
* button
* true/false
* link
* page link

= Can I use Builderius with the Meta Box Plugin? =
Certainly, the pro version works with all Meta Box fields. The free version of the builder works with simple fields only, including:

* button
* checkbox
* checkbox list
* email
* hidden
* number
* password
* radio
* range
* select
* select advanced
* text
* textarea
* url
* autocomplete
* color picker
* Google Maps
* image select
* oEmbed
* slider
* time
* wysiwyg
* file
* file advanced
* file input
* image
* image advanced
* video

= Is my site secure with Builderius? =
Yes it is, although no website is 100% secure. In fact Builderius passed a rigorous security audit when it was decided to be used for the development of the [Ukrainian Territorial Defense](https://tro.mil.gov.ua/) website (Ukrainian Military). Builderius does not suffer from the security weaknesses some of the builders do, as we use a customized GraphQL language to perform tasks others use PhP for. You can indeed run a PhP function but you do it with special helpers we have built on top of GraphQL. This method has many benefits that have nothing to do with security, but it also prevents any modification of the database. We simply block all potentially harmful PhP functions. In addition to this, Builderius prevents access to the builder to all non Administrators on a WordPress capability level, we do not simply hide the interface, we do not run it for users that do not have proper capabilities assigned to them. In conclusion, your website will not be hacked due to Builderius being installed.

= Will Builderius slow down my website? =
Absolutely not! Builderius will speed up your website. In fact we participated in the [WordPress Builders Fight Club](https://wpbuildersfightclub.org/), a community let project where popular builders were pitted against each other in building a set design, with premade assets on the same server. Builderius has had the absolute smallest DOM size, as well as other scores that were pretty high. Builderius takes performance extremely seriously and competitively. It turns off all unnecessary assets from loading on pages it is used on, it will make your websites fly.

= Will my Website Accessibility Suffer if I use Builderius? =
No it will not. Builderius is a builder that is uniquely focused on building accessible websites. For us Accessibility is not an afterthought. We are including it as an essential part of our development. Building Accessibility in the builder is an ongoing process. Builderius has been presented in WordPress Accessibility Meetup too, [watch the video and read about the event](https://equalizedigital.com/building-accessible-websites-with-builderius-elvis-krstulovic/).
We are tackling this in a three-phase process:
1. **Fundamental building blocks**: provide and promote HTML and Dynamic data features needed for building accessible pages and components. This stage enables users who know how to build accessible websites without obstacles coming from Builderius. [done]
2. **Accessible defaults**: provide convenient widgets (interactive elements), styles and other features that are accessible by default. This phase should help users who do not know how to build accessible websites, make them more accessible. [in process]
3. **Built in audit tools**: provide indicators, warnings and suggestions to tackle “low hanging fruit”: proper sequence of headings, missing alt texts, contrast errors … inside the builder, with proper references on how to fix them. [todo]

= How to report a bug? =
You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team helps validate, triage and handle any security vulnerabilities. [Report a security vulnerability](https://patchstack.com/database/vdp/builderius). For all other bugs, please use the WordPress support link provided in the sidebar.

== Screenshots ==

1. Element styling settings and page structure
2. Setting component properties
3. Linking element content to component properties
4. Selectors panel with code editor open
5. CSS Variables interface

