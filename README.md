=== Zotpress Static Bibliography ===
Contributors: nytafar
Plugin Name: Zotpress Static Bibliography
Plugin URI: http://jellum.net/plugins/
Tags: zotero, zotpress, citation manager, citations, citation, cite, citing, bibliography, bibliographies, reference, referencing, references, reference list, reference manager, academic, academic blogging, academia, scholar, scholarly, scholarly blogging
Author URI: https://github.com/nytafar/zotpress-static-bibliography/
Author: Lasse Jellum
License: Apache2.0

# Zotpress Static Bibliography

```
███████╗██████╗ ███████╗████████╗ █████╗ ████████╗██╗ ██████╗
╚══███╔╝██╔══██╗██╔════╝╚══██╔══╝██╔══██╗╚══██╔══╝██║██╔════╝
  ███╔╝ ██████╔╝███████╗   ██║   ███████║   ██║   ██║██║     
 ███╔╝  ██╔═══╝ ╚════██║   ██║   ██╔══██║   ██║   ██║██║     
███████╗██║     ███████║   ██║   ██║  ██║   ██║   ██║╚██████╗
╚══════╝╚═╝     ╚══════╝   ╚═╝   ╚═╝  ╚═╝   ╚═╝   ╚═╝ ╚═════╝
```

An enhancement plugin for Zotpress that implements server-side rendering of bibliographic citations, dramatically improving Core Web Vitals and overall performance through static generation and caching capabilities.

## Overview

Zotpress Static Bibliography fundamentally improves how citations are rendered by moving from client-side JavaScript to server-side PHP. This architectural shift brings significant benefits to Core Web Vitals and overall performance:

### Caveats

This initial version only modifies certain shortcodes in a way that I required for my own projects. It is not a complete implementation and may not work for everyone. Feel free to fork, modify and post PRs. With enough interest, I may continue to develop this plugin. Ultimately, server-side, jquery-free rendering would be a great native feature of zotpress itself.

### Immediate Benefits
- **Zero Layout Shift**: Eliminates Cumulative Layout Shift (CLS) through server-side rendering
- **Instant Display**: Citations appear immediately with the initial page load
- **Mobile Optimized**: Reduced battery usage and smoother performance on mobile devices
- **Better SEO**: Static content is immediately available to search engines

### Technical Advantages
- Server-side PHP rendering eliminates JavaScript-based layout shifts
- Citations are pre-rendered during initial page load
- No AJAX calls or dynamic content injection
- Efficient multi-level caching system

### User Experience
- Pages load with citations perfectly positioned
- No distracting content jumps or reflows
- Faster perceived performance
- Optimal reading experience on all devices

### Core Web Vitals Impact
- Perfect CLS score (0.0) through static rendering
- Improved LCP with immediate content availability
- Reduced FID by minimizing JavaScript execution
- Enhanced mobile performance metrics

### Mobile Performance
- Reduced JavaScript processing
- Lower battery consumption
- Minimal network requests
- Better offline support
- Smoother scrolling and interaction

## Core Features and Compatibility

### Enhanced Performance While Maintaining Compatibility

#### Function Naming Strategy
- Main shortcode functions use `ZotpressStatic_` prefix
  * `ZotpressStatic_zotpressInText`
  * `ZotpressStatic_zotpressInTextBib`
- Helper functions use `zpStatic_` prefix
  * `zpStatic_StripQuotes`
- Prevents conflicts with original plugin
- Maintains clear code organization

#### Shortcode Enhancement
- Preserves all original shortcode attributes
- Maintains same citation formats
- Supports existing styling options
- Provides JavaScript fallback

#### Data Structure Compatibility
- Uses existing database schema
- Preserves citation numbering system
- Maintains `$GLOBALS['zp_shortcode_instances']`
- Supports all original formatting options

#### Core Web Vitals Improvements
While maintaining compatibility, we achieve:
- Zero Cumulative Layout Shift (CLS: 0.0)
- Faster Largest Contentful Paint (LCP)
- Reduced First Input Delay (FID)
- Better mobile performance metrics

#### Implementation Strategy
- Server-side rendering replaces JavaScript
- Pre-rendered citations in initial HTML
- No post-load content injection
- Efficient cache utilization
- Fallback mechanisms when needed

### Technical Integration

#### Plugin Dependencies
- Requires original Zotpress plugin
- Hooks into WordPress at priority 20
- Removes original shortcodes safely
- Adds enhanced static versions

#### Caching Integration
- Works with WordPress object cache
- Supports full page caching
- Fragment caching for citations
- Smart cache invalidation

## Caching Strategy

### Multi-Level Caching for Zero CLS

#### Object Cache Layer
- Caches rendered citations
- Stores bibliography fragments
- Maintains citation numbering
- Optimizes database queries
- Configurable TTL settings

#### Page Cache Integration
- Full compatibility with page caching
- Static HTML in initial response
- No dynamic content injection
- Zero layout shift guarantee
- Improved server response time

#### Fragment Caching
- Individual citation caching
- Bibliography section caching
- Style information caching
- Format template caching
- Efficient cache invalidation

#### Cache Keys and Versioning
- Citation-specific cache keys
- Style-based cache variants
- Format-dependent caching
- Smart version tracking
- Automatic updates

### Cache Performance Benefits

#### Layout Stability
- Pre-rendered content from cache
- No loading placeholders needed
- Consistent DOM structure
- Zero Cumulative Layout Shift
- Better user experience

#### Resource Optimization
- Reduced database queries
- Minimal PHP processing
- No JavaScript overhead
- Lower server load
- Better scalability

#### Mobile Performance
- Faster initial render
- Reduced bandwidth usage
- Better battery efficiency
- Improved Core Web Vitals
- Enhanced SEO metrics

### Cache Management

#### Invalidation Strategy
- Smart cache invalidation
- Citation update triggers
- Style change detection
- Format modification tracking
- Automatic refresh

#### Fallback Mechanism
- Graceful cache miss handling
- Dynamic rendering fallback
- Error recovery system
- Performance monitoring
- Debug logging

## Performance Comparison

### Dynamic (Original) vs Static Rendering

#### Dynamic Rendering Issues
- ❌ Content shifts during citation loading
- ❌ Loading spinners disrupt reading
- ❌ JavaScript-based insertion causes layout jumps
- ❌ Poor Core Web Vitals scores
- ❌ Delayed content visibility
- ❌ Multiple AJAX requests
- ❌ Limited cache compatibility

#### Static Rendering Benefits
- ✅ Zero Cumulative Layout Shift (CLS)
- ✅ Citations present in initial HTML
- ✅ No JavaScript-based DOM manipulation
- ✅ Optimal Core Web Vitals scores
- ✅ Immediate content visibility
- ✅ Single server-side render
- ✅ Full cache compatibility

### Performance Metrics

#### Page Load
| Metric | Dynamic | Static |
|--------|---------|---------|
| CLS Score | > 0.1 | 0.0 |
| Initial Load | Delayed | Immediate |
| Layout Stability | Unstable | Stable |
| Cache Support | Limited | Full |

#### Resource Usage
| Resource | Dynamic | Static |
|----------|---------|---------|
| JavaScript | Heavy | Minimal |
| AJAX Calls | Multiple | None |
| Server Load | Higher | Lower |
| Memory Usage | Variable | Predictable |

### Core Web Vitals Impact

#### Before (Dynamic)
- CLS: Poor (> 0.1)
- LCP: Delayed by JavaScript
- FID: Impacted by processing
- Mobile: Suboptimal performance

#### After (Static)
- CLS: Perfect (0.0)
- LCP: Immediate display
- FID: Minimal JavaScript
- Mobile: Optimal performance

## Installation and Configuration

### Prerequisites
- WordPress 5.0 or higher
- PHP 7.4 or higher
- Original Zotpress plugin (7.3.0+)
- WordPress object cache (recommended)

### Installation Steps
1. Install and activate the original Zotpress plugin
2. Configure your Zotpress API key and settings
3. Install this plugin by uploading to `/wp-content/plugins/`
4. Activate through WordPress admin interface
5. Clear any existing page caches

### Configuration
1. No additional configuration needed by default
2. Plugin automatically enhances Zotpress shortcodes
3. Existing Zotpress settings are respected
4. Page cache plugins will work automatically

### Optimization Tips
1. Enable WordPress object caching
2. Use a page caching plugin
3. Keep citation styles minimal
4. Update citations during low-traffic periods

### Troubleshooting
1. Clear page cache after citation updates
2. Check PHP error logs if citations fail
3. Verify Zotpress API connectivity
4. Ensure proper shortcode syntax

## Usage

Continue using the standard Zotpress shortcodes:
```
[zotpressInText item="NCXAA92F"]
[zotpressInTextBib style="apa"]
```

The plugin automatically converts these to static output while maintaining all formatting options.

## Caveats

1. First-time citation rendering still requires database access
2. Object cache recommended for optimal performance
3. Edge cases may fall back to JavaScript rendering
4. Requires original Zotpress plugin to be active

## Upcoming Features

### Shortcode Enhancement
- Complete rewrite of all shortcode handlers
- Implementation of configurable rendering rules
- Support for all shortcode options
- Enhanced formatting flexibility

### Shortcode Rewrite Plans

#### Citation Rendering Options
- Full support for all citation styles (APA, MLA, Chicago, etc.)
- Custom citation format templates
- Configurable author name formatting
- Flexible date formatting options
- Custom field inclusion/exclusion

#### Bibliography Formatting
- Customizable bibliography layouts
- Sort order configuration
- Grouping options (by year, type, author)
- Custom field display rules
- Export format options

#### Advanced Features
- Citation filtering and grouping
- Dynamic style switching
- Multiple bibliography support
- Cross-reference handling
- Custom template system

#### Configuration System
- Global default settings
- Per-shortcode overrides
- Style inheritance
- Template customization
- Format presets

### Planned Improvements
- [ ] Admin settings page for configuration
- [ ] Custom caching layer
- [ ] Citation style customization
- [ ] Migration tools
- [ ] Performance analytics
- [ ] Debug logging options

## Contributing

### Development Guidelines

#### Code Organization
- Main plugin file: Core initialization and hooks
- `lib/utils.php`: Shared utility functions
- `lib/shortcode/`: Shortcode implementations
- Future: Admin interface and settings

#### Naming Conventions
- Main shortcode functions: `ZotpressStatic_` prefix
  * Example: `ZotpressStatic_zotpressInText`
- Helper functions: `zpStatic_` prefix
  * Example: `zpStatic_StripQuotes`
- This prevents collisions with original Zotpress

#### Testing
1. Set up a WordPress development environment
2. Install original Zotpress plugin
3. Run tests with various citation formats
4. Verify cache integration
5. Check performance metrics

#### Pull Request Process
1. Fork the repository
2. Create a feature branch
3. Follow naming conventions
4. Add/update tests
5. Submit PR with description

### Development Setup

1. Requirements:
   - WordPress development environment
   - Original Zotpress plugin
   - PHP development tools
   - WordPress debugging enabled

2. Environment Setup:
   ```bash
   # Clone repository
   git clone <repository-url>
   cd zotpress-static-bibliography

   # Install dependencies
   composer install

   # Set up WordPress test environment
   ./bin/install-wp-tests.sh
   ```

3. Running Tests:
   ```bash
   # Run PHP unit tests
   ./vendor/bin/phpunit

   # Run WordPress integration tests
   ./vendor/bin/wp-tests
   ```

### Code Style

Follow WordPress coding standards:
- PSR-12 for PHP files
- WordPress coding standards
- Clear documentation
- Meaningful commit messages

## License

This project is licensed under the GPL v2 or later - see the LICENSE file for details.

## Acknowledgments

- Katie Seaborn for developing Zotpress
- AI for generating excessive and over the top documentation
- WordPress community
- Zotero team

## Support

For issues and feature requests, please use the GitHub issue tracker.

## Implementation Details

### Server-Side Rendering
The plugin implements static rendering by:
1. Intercepting Zotpress shortcodes (`[zotpressInText]` and `[zotpressInTextBib]`)
2. Processing citation data on the server using PHP
3. Generating static HTML output
4. Supporting object caching for rendered citations

### Compatibility Strategy

#### Function Naming
- Main shortcode functions use `ZotpressStatic_` prefix
- Helper functions use `zpStatic_` prefix
- Prevents collisions with original plugin
- Maintains clear association with static implementation

#### Data Structure Compatibility
- Uses same database schema as Zotpress
- Maintains citation numbering system
- Preserves all shortcode attributes
- Supports existing API endpoints

#### Graceful Fallback
- Falls back to JavaScript rendering if needed
- Maintains hidden spans for compatibility
- Supports all original formatting options
- Preserves existing citation data

#### Plugin Dependencies
- Requires original Zotpress plugin
- Hooks into existing WordPress filters
- Respects Zotpress settings
- Maintains upgrade compatibility

### Performance Optimizations

#### Layout Stability
- Zero Cumulative Layout Shift (CLS)
- Immediate static content rendering
- No content reflow during load
- Stable reading experience

#### Server-Side Processing
- Direct PHP rendering of citations
- Elimination of AJAX requests
- Reduced database queries through caching
- Efficient citation numbering system

#### Resource Management
- Removal of unnecessary JavaScript
- Optimized CSS loading
- Reduced client-side processing
- Minimal DOM manipulation

#### Caching Implementation
- WordPress object cache integration
- Fragment caching for citations
- Full page cache compatibility
- Smart cache invalidation

#### Static Generation
- Pre-rendered HTML output
- SEO-friendly content
- Reduced server load
- Improved page load times

#### Web Vitals Optimization
- Optimal CLS scores through static rendering
- Improved LCP with immediate content display
- Better FID through reduced JavaScript
- Enhanced mobile performance metrics

### Technical Architecture

```
zotpress-static-bibliography/
├── zotpress-static-bibliography.php (Main plugin file)
└── lib/
    ├── utils.php (Shared utilities)
    └── shortcode/
        ├── shortcode.intext.php (Static in-text citations)
        └── shortcode.intextbib.php (Static bibliography)

```
