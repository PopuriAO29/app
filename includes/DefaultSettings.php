<?php

/**
 * @file
 *
 *                 NEVER EDIT THIS FILE
 *
 *
 * To customize your installation, edit "LocalSettings.php". If you make
 * changes here, they will be lost on next upgrade of MediaWiki!
 *
 * In this file, variables whose default values depend on other
 * variables are set to false. The actual default value of these variables
 * will only be set in Setup.php, taking into account any custom settings
 * performed in LocalSettings.php.
 *
 * Documentation is in the source and on:
 * http://www.mediawiki.org/wiki/Manual:Configuration_settings
 */
/**
 * @cond file_level_code
 * This is not a valid entry point, perform no further processing unless MEDIAWIKI is defined
 */
if (!defined('MEDIAWIKI')) {
    echo "This file is part of MediaWiki and is not a valid entry point\n";
    die(1);
}

# Create a site configuration object. Not used for much in a default install.
# Note: this (and other things) will break if the autoloader is not enabled.
# Please include includes/AutoLoader.php before including this file.
$wgConf = new SiteConfiguration;
// CONFIG_REVISION: inspect constructor and move somewhere else
/** @endcond */
/**
 * URL of the server.
 *
 * Example:
 * <code>
 * $wgServer = 'http://example.com';
 * </code>
 *
 * This is usually detected correctly by MediaWiki. If MediaWiki detects the
 * wrong server, it will redirect incorrectly after you save a page. In that
 * case, set this variable to fix it.
 *
 * If you want to use protocol-relative URLs on your wiki, set this to a
 * protocol-relative URL like '//example.com' and set $wgCanonicalServer
 * to a fully qualified URL.
 */
$wgServer = WebRequest::detectServer();
// CONFIG_REVISION: move to expansions

/* * ********************************************************************* *//**
 * @name   Script path settings
 * @{
 */
/**
 * Whether to support URLs like index.php/Page_title These often break when PHP
 * is set up in CGI mode. PATH_INFO *may* be correct if cgi.fix_pathinfo is set,
 * but then again it may not; lighttpd converts incoming path data to lowercase
 * on systems with case-insensitive filesystems, and there have been reports of
 * problems on Apache as well.
 *
 * To be safe we'll continue to keep it off by default.
 *
 * Override this to false if $_SERVER['PATH_INFO'] contains unexpectedly
 * incorrect garbage, or to true if it is really correct.
 *
 * The default $wgArticlePath will be set based on this value at runtime, but if
 * you have customized it, having this incorrectly set to true can cause
 * redirect loops when "pretty URLs" are used.
 */
$wgUsePathInfo = ( strpos(php_sapi_name(), 'cgi') === false ) &&
        ( strpos(php_sapi_name(), 'apache2filter') === false ) &&
        ( strpos(php_sapi_name(), 'isapi') === false );
// CONFIG_REVISION: move somewhere else (preferably app/LocalSettings.php)
/* * @} */

/* * ********************************************************************* *//**
 * @name   URLs and file paths
 *
 * These various web and file path variables are set to their defaults
 * in Setup.php if they are not explicitly set from LocalSettings.php.
 *
 * These will relatively rarely need to be set manually, unless you are
 * splitting style sheets or images outside the main document root.
 *
 * In this section, a "path" is usually a host-relative URL, i.e. a URL without
 * the host part, that starts with a slash. In most cases a full URL is also
 * acceptable. A "directory" is a local file path.
 *
 * In both paths and directories, trailing slashes should not be included.
 *
 * @{
 */
/**
 * The URL path of the skins directory. Will default to "{$wgScriptPath}/skins" in Setup.php
 */
$wgStylePath = false;
// CONFIG_REVISION: expansion or env-specific

/**
 * The URL path for primary article page views. This path should contain $1,
 * which is replaced by the article title.
 *
 * Will default to "{$wgScript}/$1" or "{$wgScript}?title=$1" in Setup.php,
 * depending on $wgUsePathInfo.
 */
$wgArticlePath = false;
// CONFIG_REVISION: different than Setup.php + expansion
/**
 * The maximum age of temporary (incomplete) uploaded files
 */
$wgUploadStashMaxAge = 6 * 3600; // 6 hours

/**
 * The filesystem path of the images directory. Defaults to "{$IP}/images".
 */
$wgUploadDirectory = false;

/**
 * The URL path of the wiki logo. The logo size should be 135x135 pixels.
 * Will default to "{$wgStylePath}/common/images/wiki.png" in Setup.php
 */
$wgLogo = false;

/**
 * The URL path of the shortcut icon.
 */
$wgFavicon = '/favicon.ico';

/**
 * The URL path of the icon for iPhone and iPod Touch web app bookmarks.
 * Defaults to no icon.
 */
$wgAppleTouchIcon = false;

/**
 * The local filesystem path to a temporary directory. This is not required to
 * be web accessible.
 *
 * Will default to "{$wgUploadDirectory}/tmp" in Setup.php
 */
$wgTmpDirectory = false;

/**
 * If set, this URL is added to the start of $wgUploadPath to form a complete
 * upload URL.
 */
$wgUploadBaseUrl = '';

/**
 * To enable remote on-demand scaling, set this to the thumbnail base URL.
 * Full thumbnail URL will be like $wgUploadStashScalerBaseUrl/e/e6/Foo.jpg/123px-Foo.jpg
 * where 'e6' are the first two characters of the MD5 hash of the file name.
 * If $wgUploadStashScalerBaseUrl is set to false, thumbs are rendered locally as needed.
 */
$wgUploadStashScalerBaseUrl = false;

/**
 * To set 'pretty' URL paths for actions other than
 * plain page views, add to this array. For instance:
 *   'edit' => "$wgScriptPath/edit/$1"
 *
 * There must be an appropriate script or rewrite rule
 * in place to handle these URLs.
 */
$wgActionPaths = array();

/* * @} */

/* * ********************************************************************* *//**
 * @name   Files and file uploads
 * @{
 */
/** Uploads have to be specially set up to be secure */
$wgEnableUploads = false;

/** Allows to move images and other media files */
$wgAllowImageMoving = true;

/**
 * These are additional characters that should be replaced with '-' in file names
 */
$wgIllegalFileChars = ":";

/**
 * @deprecated since 1.17 use $wgDeletedDirectory
 */
$wgFileStore = array();

/**
 * What directory to place deleted uploads in
 */
$wgDeletedDirectory = false; //  Defaults to $wgUploadDirectory/deleted

/**
 * Set this to true if you use img_auth and want the user to see details on why access failed.
 */
$wgImgAuthDetails = false;

/**
 * If this is enabled, img_auth.php will not allow image access unless the wiki
 * is private. This improves security when image uploads are hosted on a
 * separate domain.
 */
$wgImgAuthPublicTest = true;

/**
 * File repository structures
 *
 * $wgLocalFileRepo is a single repository structure, and $wgForeignFileRepos is
 * an array of such structures. Each repository structure is an associative
 * array of properties configuring the repository.
 *
 * Properties required for all repos:
 *   - class            The class name for the repository. May come from the core or an extension.
 *                      The core repository classes are FileRepo, LocalRepo, ForeignDBRepo.
 *                      FSRepo is also supported for backwards compatibility.
 *
 *   - name             A unique name for the repository (but $wgLocalFileRepo should be 'local').
 *                      The name should consist of alpha-numberic characters.
 *   - backend          A file backend name (see $wgFileBackends).
 *
 * For most core repos:
 *   - zones            Associative array of zone names that each map to an array with:
 *                          container : backend container name the zone is in
 *                          directory : root path within container for the zone
 *                      Zones default to using <repo name>-<zone> as the
 *                      container name and the container root as the zone directory.
 *   - url              Base public URL
 *   - hashLevels       The number of directory levels for hash-based division of files
 *   - thumbScriptUrl   The URL for thumb.php (optional, not recommended)
 *   - transformVia404  Whether to skip media file transformation on parse and rely on a 404
 *                      handler instead.
 *   - initialCapital   Equivalent to $wgCapitalLinks (or $wgCapitalLinkOverrides[NS_FILE],
 *                      determines whether filenames implicitly start with a capital letter.
 *                      The current implementation may give incorrect description page links
 *                      when the local $wgCapitalLinks and initialCapital are mismatched.
 *   - pathDisclosureProtection
 *                      May be 'paranoid' to remove all parameters from error messages, 'none' to
 *                      leave the paths in unchanged, or 'simple' to replace paths with
 *                      placeholders. Default for LocalRepo is 'simple'.
 *   - fileMode         This allows wikis to set the file mode when uploading/moving files. Default
 *                      is 0644.
 *   - directory        The local filesystem directory where public files are stored. Not used for
 *                      some remote repos.
 *   - thumbDir         The base thumbnail directory. Defaults to <directory>/thumb.
 *   - thumbUrl         The base thumbnail URL. Defaults to <url>/thumb.
 *
 *
 * These settings describe a foreign MediaWiki installation. They are optional, and will be ignored
 * for local repositories:
 *   - descBaseUrl       URL of image description pages, e.g. http://en.wikipedia.org/wiki/File:
 *   - scriptDirUrl      URL of the MediaWiki installation, equivalent to $wgScriptPath, e.g.
 *                       http://en.wikipedia.org/w
 *   - scriptExtension   Script extension of the MediaWiki installation, equivalent to
 *                       $wgScriptExtension, e.g. .php5 defaults to .php
 *
 *   - articleUrl        Equivalent to $wgArticlePath, e.g. http://en.wikipedia.org/wiki/$1
 *   - fetchDescription  Fetch the text of the remote file description page. Equivalent to
 *                      $wgFetchCommonsDescriptions.
 *
 * ForeignDBRepo:
 *   - dbType, dbServer, dbUser, dbPassword, dbName, dbFlags
 *                      equivalent to the corresponding member of $wgDBservers
 *   - tablePrefix       Table prefix, the foreign wiki's $wgDBprefix
 *   - hasSharedCache    True if the wiki's shared cache is accessible via the local $wgMemc
 *
 * ForeignAPIRepo:
 *   - apibase              Use for the foreign API's URL
 *   - apiThumbCacheExpiry  How long to locally cache thumbs for
 *
 * If you leave $wgLocalFileRepo set to false, Setup will fill in appropriate values.
 * Otherwise, set $wgLocalFileRepo to a repository structure as described above.
 * If you set $wgUseInstantCommons to true, it will add an entry for Commons.
 * If you set $wgForeignFileRepos to an array of repostory structures, those will
 * be searched after the local file repo.
 * Otherwise, you will only have access to local media files.
 *
 * @see Setup.php for an example usage and default initialization.
 */
$wgLocalFileRepo = false;

/** @see $wgLocalFileRepo */
$wgForeignFileRepos = array();

/**
 * Use Commons as a remote file repository. Essentially a wrapper, when this
 * is enabled $wgForeignFileRepos will point at Commons with a set of default
 * settings
 */
$wgUseInstantCommons = false;

/**
 * File backend structure configuration.
 * This is an array of file backend configuration arrays.
 * Each backend configuration has the following parameters:
 *     'name'        : A unique name for the backend
 *     'class'       : The file backend class to use
 *     'wikiId'      : A unique string that identifies the wiki (container prefix)
 *     'lockManager' : The name of a lock manager (see $wgLockManagers)
 * Additional parameters are specific to the class used.
 */
$wgFileBackends = array();

/**
 * Array of configuration arrays for each lock manager.
 * Each backend configuration has the following parameters:
 *     'name'        : A unique name for the lock manger
 *     'class'       : The lock manger class to use
 * Additional parameters are specific to the class used.
 */
$wgLockManagers = array();

/**
 * Show EXIF data, on by default if available.
 * Requires PHP's EXIF extension: http://www.php.net/manual/en/ref.exif.php
 *
 * NOTE FOR WINDOWS USERS:
 * To enable EXIF functions, add the following lines to the
 * "Windows extensions" section of php.ini:
 *
 * extension=extensions/php_mbstring.dll
 * extension=extensions/php_exif.dll
 */
$wgShowEXIF = function_exists('exif_read_data');

/**
 * If to automatically update the img_metadata field
 * if the metadata field is outdated but compatible with the current version.
 * Defaults to false.
 */
$wgUpdateCompatibleMetadata = false;

/**
 * If you operate multiple wikis, you can define a shared upload path here.
 * Uploads to this wiki will NOT be put there - they will be put into
 * $wgUploadDirectory.
 * If $wgUseSharedUploads is set, the wiki will look in the shared repository if
 * no file of the given name is found in the local repository (for [[File:..]],
 * [[Media:..]] links). Thumbnails will also be looked for and generated in this
 * directory.
 *
 * Note that these configuration settings can now be defined on a per-
 * repository basis for an arbitrary number of file repositories, using the
 * $wgForeignFileRepos variable.
 */
$wgUseSharedUploads = false;
/** Full path on the web server where shared uploads can be found */
$wgSharedUploadPath = null;
/** Fetch commons image description pages and display them on the local wiki? */
$wgFetchCommonsDescriptions = false;
/** Path on the file system where shared uploads can be found. */
$wgSharedUploadDirectory = null;
/** DB name with metadata about shared directory. Set this to false if the uploads do not come from a wiki. */
$wgSharedUploadDBname = false;
/** Optional table prefix used in database. */
$wgSharedUploadDBprefix = '';
/** Cache shared metadata in memcached. Don't do this if the commons wiki is in a different memcached domain */
$wgCacheSharedUploads = true;
/**
 * Allow for upload to be copied from an URL. Requires Special:Upload?source=web
 * The timeout for copy uploads is set by $wgHTTPTimeout.
 */
$wgAllowCopyUploads = false;

/**
 * Max size for uploads, in bytes. If not set to an array, applies to all
 * uploads. If set to an array, per upload type maximums can be set, using the
 * file and url keys. If the * key is set this value will be used as maximum
 * for non-specified types.
 *
 * For example:
 * $wgMaxUploadSize = array(
 *     '*' => 250 * 1024,
 *     'url' => 500 * 1024,
 * );
 * Sets the maximum for all uploads to 250 kB except for upload-by-url, which
 * will have a maximum of 500 kB.
 *
 */
$wgMaxUploadSize = 1024 * 1024 * 100; # 100MB

/**
 * Point the upload navigation link to an external URL
 * Useful if you want to use a shared repository by default
 * without disabling local uploads (use $wgEnableUploads = false for that)
 * e.g. $wgUploadNavigationUrl = 'http://commons.wikimedia.org/wiki/Special:Upload';
 */
$wgUploadNavigationUrl = false;

/**
 * Point the upload link for missing files to an external URL, as with
 * $wgUploadNavigationUrl. The URL will get (?|&)wpDestFile=<filename>
 * appended to it as appropriate.
 */
$wgUploadMissingFileUrl = false;

/**
 * Give a path here to use thumb.php for thumbnail generation on client request, instead of
 * generating them on render and outputting a static URL. This is necessary if some of your
 * apache servers don't have read/write access to the thumbnail path.
 *
 * Example:
 *   $wgThumbnailScriptPath = "{$wgScriptPath}/thumb{$wgScriptExtension}";
 */
$wgThumbnailScriptPath = false;
$wgSharedThumbnailScriptPath = false;

/**
 * Set this to false if you do not want MediaWiki to divide your images
 * directory into many subdirectories, for improved performance.
 *
 * It's almost always good to leave this enabled. In previous versions of
 * MediaWiki, some users set this to false to allow images to be added to the
 * wiki by simply copying them into $wgUploadDirectory and then running
 * maintenance/rebuildImages.php to register them in the database. This is no
 * longer recommended, use maintenance/importImages.php instead.
 *
 * Note that this variable may be ignored if $wgLocalFileRepo is set.
 */
$wgHashedUploadDirectory = true;

/**
 * Set the following to false especially if you have a set of files that need to
 * be accessible by all wikis, and you do not want to use the hash (path/a/aa/)
 * directory layout.
 */
$wgHashedSharedUploadDirectory = true;

/**
 * Base URL for a repository wiki. Leave this blank if uploads are just stored
 * in a shared directory and not meant to be accessible through a separate wiki.
 * Otherwise the image description pages on the local wiki will link to the
 * image description page on this wiki.
 *
 * Please specify the namespace, as in the example below.
 */
$wgRepositoryBaseUrl = "https://commons.wikimedia.org/wiki/File:";

/**
 * This is the list of preferred extensions for uploading files. Uploading files
 * with extensions not in this list will trigger a warning.
 *
 * WARNING: If you add any OpenOffice or Microsoft Office file formats here,
 * such as odt or doc, and untrusted users are allowed to upload files, then
 * your wiki will be vulnerable to cross-site request forgery (CSRF).
 */
$wgFileExtensions = array('png', 'gif', 'jpg', 'jpeg');

/** Files with these extensions will never be allowed as uploads. */
$wgFileBlacklist = array(
    # HTML may contain cookie-stealing JavaScript and web bugs
    'html', 'htm', 'js', 'jsb', 'mhtml', 'mht', 'xhtml', 'xht',
    # PHP scripts may execute arbitrary code on the server
    'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
    # Other types that may be interpreted by some servers
    'shtml', 'jhtml', 'pl', 'py', 'cgi',
    # May contain harmful executables for Windows victims
    'exe', 'scr', 'dll', 'msi', 'vbs', 'bat', 'com', 'pif', 'cmd', 'vxd', 'cpl');

/**
 * Files with these mime types will never be allowed as uploads
 * if $wgVerifyMimeType is enabled.
 */
$wgMimeTypeBlacklist = array(
    # HTML may contain cookie-stealing JavaScript and web bugs
    'text/html', 'text/javascript', 'text/x-javascript', 'application/x-shellscript',
    # PHP scripts may execute arbitrary code on the server
    'application/x-php', 'text/x-php',
    # Other types that may be interpreted by some servers
    'text/x-python', 'text/x-perl', 'text/x-bash', 'text/x-sh', 'text/x-csh',
    # Client-side hazards on Internet Explorer
    'text/scriptlet', 'application/x-msdownload',
    # Windows metafile, client-side vulnerability on some systems
    'application/x-msmetafile',
);

/**
 * Allow Java archive uploads.
 * This is not recommended for public wikis since a maliciously-constructed
 * applet running on the same domain as the wiki can steal the user's cookies.
 */
$wgAllowJavaUploads = false;

/**
 * This is a flag to determine whether or not to check file extensions on upload.
 *
 * WARNING: setting this to false is insecure for public wikis.
 */
$wgCheckFileExtensions = true;

/**
 * If this is turned off, users may override the warning for files not covered
 * by $wgFileExtensions.
 *
 * WARNING: setting this to false is insecure for public wikis.
 */
$wgStrictFileExtensions = true;

/**
 * Setting this to true will disable the upload system's checks for HTML/JavaScript.
 * THIS IS VERY DANGEROUS on a publicly editable site, so USE wgGroupPermissions
 * TO RESTRICT UPLOADING to only those that you trust
 */
$wgDisableUploadScriptChecks = false;

/** Warn if uploaded files are larger than this (in bytes), or false to disable */
$wgUploadSizeWarning = false;

/**
 * list of trusted media-types and mime types.
 * Use the MEDIATYPE_xxx constants to represent media types.
 * This list is used by File::isSafeFile
 *
 * Types not listed here will have a warning about unsafe content
 * displayed on the images description page. It would also be possible
 * to use this for further restrictions, like disabling direct
 * [[media:...]] links for non-trusted formats.
 */
$wgTrustedMediaFormats = array(
    MEDIATYPE_BITMAP, //all bitmap formats
    MEDIATYPE_AUDIO, //all audio formats
    MEDIATYPE_VIDEO, //all plain video formats
    "image/svg+xml", //svg (only needed if inline rendering of svg is not supported)
    "application/pdf", //PDF files
        #"application/x-shockwave-flash", //flash/shockwave movie
);

/**
 * Plugins for media file type handling.
 * Each entry in the array maps a MIME type to a class name
 */
$wgMediaHandlers = array(
    'image/jpeg' => 'JpegHandler',
    'image/png' => 'PNGHandler',
    'image/gif' => 'GIFHandler',
    'image/tiff' => 'TiffHandler',
    'image/x-ms-bmp' => 'BmpHandler',
    'image/x-bmp' => 'BmpHandler',
    'image/x-xcf' => 'XCFHandler',
    'image/svg+xml' => 'SvgHandler', // official
    'image/svg' => 'SvgHandler', // compat
    'image/vnd.djvu' => 'DjVuHandler', // official
    'image/x.djvu' => 'DjVuHandler', // compat
    'image/x-djvu' => 'DjVuHandler', // compat
);

/**
 * Resizing can be done using PHP's internal image libraries or using
 * ImageMagick or another third-party converter, e.g. GraphicMagick.
 * These support more file formats than PHP, which only supports PNG,
 * GIF, JPG, XBM and WBMP.
 *
 * Use Image Magick instead of PHP builtin functions.
 */
$wgUseImageMagick = false;
/** The convert command shipped with ImageMagick */
$wgImageMagickConvertCommand = '/usr/bin/convert';
/** The identify command shipped with ImageMagick */
$wgImageMagickIdentifyCommand = '/usr/bin/identify';

/** Sharpening parameter to ImageMagick */
$wgSharpenParameter = '0x0.4';

/** Reduction in linear dimensions below which sharpening will be enabled */
$wgSharpenReductionThreshold = 0.85;

/**
 * Temporary directory used for ImageMagick. The directory must exist. Leave
 * this set to false to let ImageMagick decide for itself.
 */
$wgImageMagickTempDir = false;

/**
 * Use another resizing converter, e.g. GraphicMagick
 * %s will be replaced with the source path, %d with the destination
 * %w and %h will be replaced with the width and height.
 *
 * Example for GraphicMagick:
 * <code>
 * $wgCustomConvertCommand = "gm convert %s -resize %wx%h %d"
 * </code>
 *
 * Leave as false to skip this.
 */
$wgCustomConvertCommand = false;

/**
 * Minimum upload chunk size, in bytes. When using chunked upload, non-final
 * chunks smaller than this will be rejected. May be reduced based on the
 * 'upload_max_filesize' or 'post_max_size' PHP settings.
 * @since 1.26
 */
$wgMinUploadChunkSize = 1024; # 1KB

/**
 * Some tests and extensions use exiv2 to manipulate the EXIF metadata in some image formats.
 */
$wgExiv2Command = '/usr/bin/exiv2';

/**
 * Scalable Vector Graphics (SVG) may be uploaded as images.
 * Since SVG support is not yet standard in browsers, it is
 * necessary to rasterize SVGs to PNG as a fallback format.
 *
 * An external program is required to perform this conversion.
 * If set to an array, the first item is a PHP callable and any further items
 * are passed as parameters after $srcPath, $dstPath, $width, $height
 */
$wgSVGConverters = array(
    'ImageMagick' => '$path/convert -background white -thumbnail $widthx$height\! $input PNG:$output',
    'sodipodi' => '$path/sodipodi -z -w $width -f $input -e $output',
    'inkscape' => '$path/inkscape -z -w $width -f $input -e $output',
    'batik' => 'java -Djava.awt.headless=true -jar $path/batik-rasterizer.jar -w $width -d $output $input',
    'rsvg' => '$path/rsvg -w$width -h$height $input $output',
    'imgserv' => '$path/imgserv-wrapper -i svg -o png -w$width $input $output',
    'ImagickExt' => array('SvgHandler::rasterizeImagickExt'),
);
/** Pick a converter defined in $wgSVGConverters */
$wgSVGConverter = 'ImageMagick';
/** If not in the executable PATH, specify the SVG converter path. */
$wgSVGConverterPath = '';
/** Don't scale a SVG larger than this */
$wgSVGMaxSize = 2048;
/** Don't read SVG metadata beyond this point.
 * Default is 1024*256 bytes */
$wgSVGMetadataCutoff = 262144;

/**
 * MediaWiki will reject HTMLesque tags in uploaded files due to idiotic browsers which can't
 * perform basic stuff like MIME detection and which are vulnerable to further idiots uploading
 * crap files as images. When this directive is on, <title> will be allowed in files with
 * an "image/svg+xml" MIME type. You should leave this disabled if your web server is misconfigured
 * and doesn't send appropriate MIME types for SVG images.
 */
$wgAllowTitlesInSVG = false;

/**
 * The maximum number of pixels a source image can have if it is to be scaled
 * down by a scaler that requires the full source image to be decompressed
 * and stored in decompressed form, before the thumbnail is generated.
 *
 * This provides a limit on memory usage for the decompression side of the
 * image scaler. The limit is used when scaling PNGs with any of the
 * built-in image scalers, such as ImageMagick or GD. It is ignored for
 * JPEGs with ImageMagick, and when using the VipsScaler extension.
 *
 * The default is 50 MB if decompressed to RGBA form, which corresponds to
 * 12.5 million pixels or 3500x3500.
 */
$wgMaxImageArea = 1.25e7;
/**
 * Force thumbnailing of animated GIFs above this size to a single
 * frame instead of an animated thumbnail.  As of MW 1.17 this limit
 * is checked against the total size of all frames in the animation.
 * It probably makes sense to keep this equal to $wgMaxImageArea.
 */
$wgMaxAnimatedGifArea = 1.25e7;
/**
 * Browsers don't support TIFF inline generally...
 * For inline display, we need to convert to PNG or JPEG.
 * Note scaling should work with ImageMagick, but may not with GD scaling.
 *
 * Example:
 * <code>
 *  // PNG is lossless, but inefficient for photos
 *  $wgTiffThumbnailType = array( 'png', 'image/png' );
 *  // JPEG is good for photos, but has no transparency support. Bad for diagrams.
 *  $wgTiffThumbnailType = array( 'jpg', 'image/jpeg' );
 * </code>
 */
$wgTiffThumbnailType = false;

/**
 * If rendered thumbnail files are older than this timestamp, they
 * will be rerendered on demand as if the file didn't already exist.
 * Update if there is some need to force thumbs and SVG rasterizations
 * to rerender, such as fixes to rendering bugs.
 */
$wgThumbnailEpoch = '20030516000000';

/**
 * If set, inline scaled images will still produce <img> tags ready for
 * output instead of showing an error message.
 *
 * This may be useful if errors are transitory, especially if the site
 * is configured to automatically render thumbnails on request.
 *
 * On the other hand, it may obscure error conditions from debugging.
 * Enable the debug log or the 'thumbnail' log group to make sure errors
 * are logged to a file for review.
 */
$wgIgnoreImageErrors = false;

/**
 * Allow thumbnail rendering on page view. If this is false, a valid
 * thumbnail URL is still output, but no file will be created at
 * the target location. This may save some time if you have a
 * thumb.php or 404 handler set up which is faster than the regular
 * webserver(s).
 */
$wgGenerateThumbnailOnParse = true;

/**
 * Show thumbnails for old images on the image description page
 */
$wgShowArchiveThumbnails = true;

/** Obsolete, always true, kept for compatibility with extensions */
$wgUseImageResize = true;

/**
 * If set to true, images that contain certain the exif orientation tag will
 * be rotated accordingly. If set to null, try to auto-detect whether a scaler
 * is available that can rotate.
 */
$wgEnableAutoRotation = null;

/**
 * Internal name of virus scanner. This servers as a key to the
 * $wgAntivirusSetup array. Set this to NULL to disable virus scanning. If not
 * null, every file uploaded will be scanned for viruses.
 */
$wgAntivirus = null;

/**
 * Configuration for different virus scanners. This an associative array of
 * associative arrays. It contains one setup array per known scanner type.
 * The entry is selected by $wgAntivirus, i.e.
 * valid values for $wgAntivirus are the keys defined in this array.
 *
 * The configuration array for each scanner contains the following keys:
 * "command", "codemap", "messagepattern":
 *
 * "command" is the full command to call the virus scanner - %f will be
 * replaced with the name of the file to scan. If not present, the filename
 * will be appended to the command. Note that this must be overwritten if the
 * scanner is not in the system path; in that case, plase set
 * $wgAntivirusSetup[$wgAntivirus]['command'] to the desired command with full
 * path.
 *
 * "codemap" is a mapping of exit code to return codes of the detectVirus
 * function in SpecialUpload.
 *   - An exit code mapped to AV_SCAN_FAILED causes the function to consider
 *     the scan to be failed. This will pass the file if $wgAntivirusRequired
 *     is not set.
 *   - An exit code mapped to AV_SCAN_ABORTED causes the function to consider
 *     the file to have an usupported format, which is probably imune to
 *     virusses. This causes the file to pass.
 *   - An exit code mapped to AV_NO_VIRUS will cause the file to pass, meaning
 *     no virus was found.
 *   - All other codes (like AV_VIRUS_FOUND) will cause the function to report
 *     a virus.
 *   - You may use "*" as a key in the array to catch all exit codes not mapped otherwise.
 *
 * "messagepattern" is a perl regular expression to extract the meaningful part of the scanners
 * output. The relevant part should be matched as group one (\1).
 * If not defined or the pattern does not match, the full message is shown to the user.
 */
$wgAntivirusSetup = array(
    #setup for clamav
    'clamav' => array(
        'command' => "clamscan --no-summary ",
        'codemap' => array(
            "0" => AV_NO_VIRUS, # no virus
            "1" => AV_VIRUS_FOUND, # virus found
            "52" => AV_SCAN_ABORTED, # unsupported file format (probably imune)
            "*" => AV_SCAN_FAILED, # else scan failed
        ),
        'messagepattern' => '/.*?:(.*)/sim',
    ),
    #setup for f-prot
    'f-prot' => array(
        'command' => "f-prot ",
        'codemap' => array(
            "0" => AV_NO_VIRUS, # no virus
            "3" => AV_VIRUS_FOUND, # virus found
            "6" => AV_VIRUS_FOUND, # virus found
            "*" => AV_SCAN_FAILED, # else scan failed
        ),
        'messagepattern' => '/.*?Infection:(.*)$/m',
    ),
);


/** Determines if a failed virus scan (AV_SCAN_FAILED) will cause the file to be rejected. */
$wgAntivirusRequired = true;

/** Determines if the mime type of uploaded files should be checked */
$wgVerifyMimeType = true;

/** Sets the mime type definition file to use by MimeMagic.php. */
$wgMimeTypeFile = "includes/mime.types";
#$wgMimeTypeFile= "/etc/mime.types";
#$wgMimeTypeFile= null; #use built-in defaults only.

/** Sets the mime type info file to use by MimeMagic.php. */
$wgMimeInfoFile = "includes/mime.info";
#$wgMimeInfoFile= null; #use built-in defaults only.

/**
 * Switch for loading the FileInfo extension by PECL at runtime.
 * This should be used only if fileinfo is installed as a shared object
 * or a dynamic library.
 */
$wgLoadFileinfoExtension = false;

/** Sets an external mime detector program. The command must print only
 * the mime type to standard output.
 * The name of the file to process will be appended to the command given here.
 * If not set or NULL, mime_content_type will be used if available.
 * Example:
 * <code>
 * #$wgMimeDetectorCommand = "file -bi"; # use external mime detector (Linux)
 * </code>
 */
$wgMimeDetectorCommand = null;

/**
 * Switch for trivial mime detection. Used by thumb.php to disable all fancy
 * things, because only a few types of images are needed and file extensions
 * can be trusted.
 */
$wgTrivialMimeDetection = false;

/**
 * Additional XML types we can allow via mime-detection.
 * array = ( 'rootElement' => 'associatedMimeType' )
 */
$wgXMLMimeTypes = array(
    'http://www.w3.org/2000/svg:svg' => 'image/svg+xml',
    'svg' => 'image/svg+xml',
    'http://www.lysator.liu.se/~alla/dia/:diagram' => 'application/x-dia-diagram',
    'http://www.w3.org/1999/xhtml:html' => 'text/html', // application/xhtml+xml?
    'html' => 'text/html', // application/xhtml+xml?
);

/**
 * Limit images on image description pages to a user-selectable limit. In order
 * to reduce disk usage, limits can only be selected from a list.
 * The user preference is saved as an array offset in the database, by default
 * the offset is set with $wgDefaultUserOptions['imagesize']. Make sure you
 * change it if you alter the array (see bug 8858).
 * This is the list of settings the user can choose from:
 */
$wgImageLimits = array(
    array(320, 240),
    array(640, 480),
    array(800, 600),
    array(1024, 768),
    array(1280, 1024),
    array(10000, 10000)
);

/**
 * Adjust thumbnails on image pages according to a user setting. In order to
 * reduce disk usage, the values can only be selected from a list. This is the
 * list of settings the user can choose from:
 */
$wgThumbLimits = array(
    120,
    150,
    180,
    200,
    250,
    300
);

/**
 * Default parameters for the <gallery> tag
 */
$wgGalleryOptions = array(
    'imagesPerRow' => 0, // Default number of images per-row in the gallery. 0 -> Adapt to screensize
    'imageWidth' => 120, // Width of the cells containing images in galleries (in "px")
    'imageHeight' => 120, // Height of the cells containing images in galleries (in "px")
    'captionLength' => 25, // Length of caption to truncate (in characters)
    'showBytes' => true, // Show the filesize in bytes in categories
);

/**
 * Adjust width of upright images when parameter 'upright' is used
 * This allows a nicer look for upright images without the need to fix the width
 * by hardcoded px in wiki sourcecode.
 */
$wgThumbUpright = 0.75;

/**
 * Default value for chmoding of new directories.
 */
$wgDirectoryMode = 0777;

/**
 * DJVU settings
 * Path of the djvudump executable
 * Enable this and $wgDjvuRenderer to enable djvu rendering
 */
# $wgDjvuDump = 'djvudump';
$wgDjvuDump = null;

/**
 * Path of the ddjvu DJVU renderer
 * Enable this and $wgDjvuDump to enable djvu rendering
 */
# $wgDjvuRenderer = 'ddjvu';
$wgDjvuRenderer = null;

/**
 * Path of the djvutxt DJVU text extraction utility
 * Enable this and $wgDjvuDump to enable text layer extraction from djvu files
 */
# $wgDjvuTxt = 'djvutxt';
$wgDjvuTxt = null;

/**
 * Path of the djvutoxml executable
 * This works like djvudump except much, much slower as of version 3.5.
 *
 * For now I recommend you use djvudump instead. The djvuxml output is
 * probably more stable, so we'll switch back to it as soon as they fix
 * the efficiency problem.
 * http://sourceforge.net/tracker/index.php?func=detail&aid=1704049&group_id=32953&atid=406583
 */
# $wgDjvuToXML = 'djvutoxml';
$wgDjvuToXML = null;


/**
 * Shell command for the DJVU post processor
 * Default: pnmtopng, since ddjvu generates ppm output
 * Set this to false to output the ppm file directly.
 */
$wgDjvuPostProcessor = 'pnmtojpeg';
/**
 * File extension for the DJVU post processor output
 */
$wgDjvuOutputExtension = 'jpg';

/** @} */ # end of file uploads }

/* * ********************************************************************* *//**
 * @name   Email settings
 * @{
 */
$serverName = substr($wgServer, strrpos($wgServer, '/') + 1);

/**
 * Site admin email address.
 */
$wgEmergencyContact = 'wikiadmin@' . $serverName;

/**
 * Password reminder email address.
 *
 * The address we should use as sender when a user is requesting his password.
 */
$wgPasswordSender = 'apache@' . $serverName;

unset($serverName); # Don't leak local variables to global scope

/**
 * Password reminder name
 */
$wgPasswordSenderName = 'MediaWiki Mail';

/**
 * Dummy address which should be accepted during mail send action.
 * It might be necessary to adapt the address or to set it equal
 * to the $wgEmergencyContact address.
 */
$wgNoReplyAddress = 'reply@not.possible';

/**
 * Set to true to enable the e-mail basic features:
 * Password reminders, etc. If sending e-mail on your
 * server doesn't work, you might want to disable this.
 */
$wgEnableEmail = true;

/**
 * Set to true to enable user-to-user e-mail.
 * This can potentially be abused, as it's hard to track.
 */
$wgEnableUserEmail = true;

/**
 * The time, in seconds, when an email confirmation email expires
 */
$wgUserEmailConfirmationTokenExpiry = 7 * 24 * 60 * 60;

/**
 * SMTP Mode
 * For using a direct (authenticated) SMTP server connection.
 * Default to false or fill an array :
 * <code>
 * "host" => 'SMTP domain',
 * "IDHost" => 'domain for MessageID',
 * "port" => "25",
 * "auth" => true/false,
 * "username" => user,
 * "password" => password
 * </code>
 */
$wgSMTP = false;

/**
 * Additional email parameters, will be passed as the last argument to mail() call.
 * If using safe_mode this has no effect
 */
$wgAdditionalMailParams = null;

/**
 * True: from page editor if s/he opted-in. False: Enotif mails appear to come
 * from $wgEmergencyContact
 */
$wgEnotifFromEditor = false;

// TODO move UPO to preferences probably ?
# If set to true, users get a corresponding option in their preferences and can choose to enable or disable at their discretion
# If set to false, the corresponding input form on the user preference page is suppressed
# It call this to be a "user-preferences-option (UPO)"

/**
 * Require email authentication before sending mail to an email addres. This is
 * highly recommended. It prevents MediaWiki from being used as an open spam
 * relay.
 */
$wgEmailAuthentication = true;

/**
 * Allow users to enable email notification ("enotif") on watchlist changes.
 */
$wgEnotifWatchlist = false;

/**
 * Allow users to enable email notification ("enotif") on Discussions changes.
 */
$wgEnotifDiscussions = true;

/**
 * Allow users to enable email notification ("enotif") when someone edits their
 * user talk page.
 */
$wgEnotifUserTalk = false;

/**
 * Set the Reply-to address in notifications to the editor's address, if user
 * allowed this in the preferences.
 */
$wgEnotifRevealEditorAddress = false;

/**
 * Send notification mails on minor edits to watchlist pages. This is enabled
 * by default. Does not affect user talk notifications.
 */
$wgEnotifMinorEdits = true;

/**
 * Send a generic mail instead of a personalised mail for each user.  This
 * always uses UTC as the time zone, and doesn't include the username.
 *
 * For pages with many users watching, this can significantly reduce mail load.
 * Has no effect when using sendmail rather than SMTP.
 */
$wgEnotifImpersonal = false;

/**
 * Maximum number of users to mail at once when using impersonal mail. Should
 * match the limit on your mail server.
 */
$wgEnotifMaxRecips = 500;

/**
 * Use real name instead of username in e-mail "from" field.
 */
$wgEnotifUseRealName = false;

/**
 * Array of usernames who will be sent a notification email for every change
 * which occurs on a wiki. Users will not be notified of their own changes.
 */
$wgUsersNotifiedOnAllChanges = array();


/** @} */ # end of email settings

/* * ********************************************************************* *//**
 * @name   Database settings
 * @{
 */
/** Database host name or IP address */
$wgDBserver = 'localhost';
/** Database port number (for PostgreSQL) */
$wgDBport = 5432;
/** Name of the database */
$wgDBname = 'my_wiki';
/** Database username */
$wgDBuser = 'wikiuser';
/** Database user's password */
$wgDBpassword = '';
/** Database type */
$wgDBtype = 'mysql';

/** Separate username for maintenance tasks. Leave as null to use the default. */
$wgDBadminuser = null;
/** Separate password for maintenance tasks. Leave as null to use the default. */
$wgDBadminpassword = null;

/**
 * Search type.
 * Leave as null to select the default search engine for the
 * selected database type (eg SearchMySQL), or set to a class
 * name to override to a custom search engine.
 */
$wgSearchType = null;

/** Table name prefix */
$wgDBprefix = '';
/** MySQL table options to use during installation or update */
$wgDBTableOptions = 'ENGINE=InnoDB';

/**
 * SQL Mode - default is turning off all modes, including strict, if set.
 * null can be used to skip the setting for performance reasons and assume
 * DBA has done his best job.
 * String override can be used for some additional fun :-)
 */
$wgSQLMode = null;

/** Mediawiki schema */
$wgDBmwschema = 'mediawiki';

/** To override default SQLite data directory ($docroot/../data) */
$wgSQLiteDataDir = '';

/**
 * Make all database connections secretly go to localhost. Fool the load balancer
 * thinking there is an arbitrarily large cluster of servers to connect to.
 * Useful for debugging.
 */
$wgAllDBsAreLocalhost = false;

/**
 * Shared database for multiple wikis. Commonly used for storing a user table
 * for single sign-on. The server for this database must be the same as for the
 * main database.
 *
 * For backwards compatibility the shared prefix is set to the same as the local
 * prefix, and the user table is listed in the default list of shared tables.
 * The user_properties table is also added so that users will continue to have their
 * preferences shared (preferences were stored in the user table prior to 1.16)
 *
 * $wgSharedTables may be customized with a list of tables to share in the shared
 * datbase. However it is advised to limit what tables you do share as many of
 * MediaWiki's tables may have side effects if you try to share them.
 * EXPERIMENTAL
 *
 * $wgSharedPrefix is the table prefix for the shared database. It defaults to
 * $wgDBprefix.
 */
$wgSharedDB = null;

/** @see $wgSharedDB */
$wgSharedPrefix = false;
/** @see $wgSharedDB */
$wgSharedTables = array('user', 'user_properties');

/**
 * Database load balancer
 * This is a two-dimensional array, an array of server info structures
 * Fields are:
 *   - host:        Host name
 *   - dbname:      Default database name
 *   - user:        DB user
 *   - password:    DB password
 *   - type:        "mysql" or "postgres"
 *   - load:        ratio of DB_SLAVE load, must be >=0, the sum of all loads must be >0
 *   - groupLoads:  array of load ratios, the key is the query group name. A query may belong
 *                  to several groups, the most specific group defined here is used.
 *
 *   - flags:       bit field
 *                  - DBO_DEFAULT -- turns on DBO_TRX only if !$wgCommandLineMode (recommended)
 *                  - DBO_DEBUG -- equivalent of $wgDebugDumpSql
 *                  - DBO_TRX -- wrap entire request in a transaction
 *                  - DBO_IGNORE -- ignore errors (not useful in LocalSettings.php)
 *                  - DBO_NOBUFFER -- turn off buffering (not useful in LocalSettings.php)
 *
 *   - max lag:     (optional) Maximum replication lag before a slave will taken out of rotation
 *   - max threads: (optional) Maximum number of running threads
 *
 *   These and any other user-defined properties will be assigned to the mLBInfo member
 *   variable of the Database object.
 *
 * Leave at false to use the single-server variables above. If you set this
 * variable, the single-server variables will generally be ignored (except
 * perhaps in some command-line scripts).
 *
 * The first server listed in this array (with key 0) will be the master. The
 * rest of the servers will be slaves. To prevent writes to your slaves due to
 * accidental misconfiguration or MediaWiki bugs, set read_only=1 on all your
 * slaves in my.cnf. You can set read_only mode at runtime using:
 *
 * <code>
 *     SET @@read_only=1;
 * </code>
 *
 * Since the effect of writing to a slave is so damaging and difficult to clean
 * up, we at Wikimedia set read_only=1 in my.cnf on all our DB servers, even
 * our masters, and then set read_only=0 on masters at runtime.
 */
$wgDBservers = false;

/**
 * Load balancer factory configuration
 * To set up a multi-master wiki farm, set the class here to something that
 * can return a LoadBalancer with an appropriate master on a call to getMainLB().
 * The class identified here is responsible for reading $wgDBservers,
 * $wgDBserver, etc., so overriding it may cause those globals to be ignored.
 *
 * The LBFactory_Multi class is provided for this purpose, please see
 * includes/db/LBFactory_Multi.php for configuration information.
 */
$wgLBFactoryConf = array('class' => 'LBFactory_Simple');

/** How long to wait for a slave to catch up to the master */
$wgMasterWaitTimeout = 10;

/** File to log database errors to */
$wgDBerrorLog = false;

/** When to give an error message */
$wgDBClusterTimeout = 10;

/**
 * Scale load balancer polling time so that under overload conditions, the database server
 * receives a SHOW STATUS query at an average interval of this many microseconds
 */
$wgDBAvgStatusPoll = 2000;

/**
 * Set to true to engage MySQL 4.1/5.0 charset-related features;
 * for now will just cause sending of 'SET NAMES=utf8' on connect.
 *
 * WARNING: THIS IS EXPERIMENTAL!
 *
 * May break if you're not using the table defs from mysql5/tables.sql.
 * May break if you're upgrading an existing wiki if set differently.
 * Broken symptoms likely to include incorrect behavior with page titles,
 * usernames, comments etc containing non-ASCII characters.
 * Might also cause failures on the object cache and other things.
 *
 * Even correct usage may cause failures with Unicode supplementary
 * characters (those not in the Basic Multilingual Plane) unless MySQL
 * has enhanced their Unicode support.
 */
$wgDBmysql5 = false;

/**
 * Other wikis on this site, can be administered from a single developer
 * account.
 * Array numeric key => database name
 */
$wgLocalDatabases = array();

/**
 * If lag is higher than $wgSlaveLagWarning, show a warning in some special
 * pages (like watchlist).  If the lag is higher than $wgSlaveLagCritical,
 * show a more obvious warning.
 */
$wgSlaveLagWarning = 10;
/** @see $wgSlaveLagWarning */
$wgSlaveLagCritical = 30;

/**
 * Use old names for change_tags indices.
 */
$wgOldChangeTagsIndex = false;

/* * @} */ # End of DB settings }


/* * ********************************************************************* *//**
 * @name   Text storage
 * @{
 */
/**
 * We can also compress text stored in the 'text' table. If this is set on, new
 * revisions will be compressed on page save if zlib support is available. Any
 * compressed revisions will be decompressed on load regardless of this setting
 * *but will not be readable at all* if zlib support is not available.
 */
$wgCompressRevisions = false;

/**
 * External stores allow including content
 * from non database sources following URL links
 *
 * Short names of ExternalStore classes may be specified in an array here:
 * $wgExternalStores = array("http","file","custom")...
 *
 * CAUTION: Access to database might lead to code execution
 */
$wgExternalStores = false;

/**
 * An array of external mysql servers, e.g.
 * $wgExternalServers = array( 'cluster1' => array( 'srv28', 'srv29', 'srv30' ) );
 * Used by LBFactory_Simple, may be ignored if $wgLBFactoryConf is set to another class.
 */
$wgExternalServers = array();

/**
 * The place to put new revisions, false to put them in the local text table.
 * Part of a URL, e.g. DB://cluster1
 *
 * Can be an array instead of a single string, to enable data distribution. Keys
 * must be consecutive integers, starting at zero. Example:
 *
 * $wgDefaultExternalStore = array( 'DB://cluster1', 'DB://cluster2' );
 *
 * @var array
 */
$wgDefaultExternalStore = false;

/**
 * Revision text may be cached in $wgMemc to reduce load on external storage
 * servers and object extraction overhead for frequently-loaded revisions.
 *
 * Set to 0 to disable, or number of seconds before cache expiry.
 */
$wgRevisionCacheExpiry = 86400 * 30; // a month

/** @} */ # end text storage }

/* * ********************************************************************* *//**
 * @name   Performance hacks and limits
 * @{
 */
/** Disable database-intensive features */
$wgMiserMode = false;
/** Disable all query pages if miser mode is on, not just some */
$wgDisableQueryPages = false;
/** Number of rows to cache in 'querycache' table when miser mode is on */
$wgQueryCacheLimit = 1000;
/** Number of links to a page required before it is deemed "wanted" */
$wgWantedPagesThreshold = 1;
/** Enable slow parser functions */
$wgAllowSlowParserFunctions = false;
/** Allow schema updates */
$wgAllowSchemaUpdates = true;

/**
 * Do DELETE/INSERT for link updates instead of incremental
 */
$wgUseDumbLinkUpdate = false;

/**
 * Anti-lock flags - bitfield
 *   - ALF_PRELOAD_LINKS:
 *       Preload links during link update for save
 *   - ALF_PRELOAD_EXISTENCE:
 *       Preload cur_id during replaceLinkHolders
 *   - ALF_NO_LINK_LOCK:
 *       Don't use locking reads when updating the link table. This is
 *       necessary for wikis with a high edit rate for performance
 *       reasons, but may cause link table inconsistency
 *   - ALF_NO_BLOCK_LOCK:
 *       As for ALF_LINK_LOCK, this flag is a necessity for high-traffic
 *       wikis.
 */
$wgAntiLockFlags = 0;

/**
 * Maximum article size in kilobytes
 */
$wgMaxArticleSize = 2048;

/**
 * The minimum amount of memory that MediaWiki "needs"; MediaWiki will try to
 * raise PHP's memory limit if it's below this amount.
 */
$wgMemoryLimit = "50M";

/** @} */ # end performance hacks }

/* * ********************************************************************* *//**
 * @name   Cache settings
 * @{
 */
/**
 * Directory for caching data in the local filesystem. Should not be accessible
 * from the web. Set this to false to not use any local caches.
 *
 * Note: if multiple wikis share the same localisation cache directory, they
 * must all have the same set of extensions. You can set a directory just for
 * the localisation cache using $wgLocalisationCacheConf['storeDirectory'].
 */
$wgCacheDirectory = false;

/**
 * Main cache type. This should be a cache with fast access, but it may have
 * limited space. By default, it is disabled, since the database is not fast
 * enough to make it worthwhile.
 *
 * The options are:
 *
 *   - CACHE_ANYTHING:   Use anything, as long as it works
 *   - CACHE_NONE:       Do not cache
 *   - CACHE_MEMCACHED:  MemCached, must specify servers in $wgMemCachedServers
 *   - CACHE_ACCEL:      APC, XCache or WinCache
 *   - CACHE_DBA:        Use PHP's DBA extension to store in a DBM-style
 *                       database. This is slow, and is not recommended for
 *                       anything other than debugging.
 *   - (other):          A string may be used which identifies a cache
 *                       configuration in $wgObjectCaches.
 *
 * @see $wgMessageCacheType, $wgParserCacheType
 */
$wgMainCacheType = CACHE_NONE;

/**
 * The cache type for storing the contents of the MediaWiki namespace. This
 * cache is used for a small amount of data which is expensive to regenerate.
 *
 * For available types see $wgMainCacheType.
 */
$wgMessageCacheType = CACHE_ANYTHING;

/**
 * The cache type for storing article HTML. This is used to store data which
 * is expensive to regenerate, and benefits from having plenty of storage space.
 *
 * For available types see $wgMainCacheType.
 */
$wgParserCacheType = CACHE_ANYTHING;

/**
 * Advanced object cache configuration.
 *
 * Use this to define the class names and constructor parameters which are used
 * for the various cache types. Custom cache types may be defined here and
 * referenced from $wgMainCacheType, $wgMessageCacheType or $wgParserCacheType.
 *
 * The format is an associative array where the key is a cache identifier, and
 * the value is an associative array of parameters. The "class" parameter is the
 * class name which will be used. Alternatively, a "factory" parameter may be
 * given, giving a callable function which will generate a suitable cache object.
 *
 * The other parameters are dependent on the class used.
 * - CACHE_DBA uses $wgTmpDirectory by default. The 'dir' parameter let you
 *   overrides that.
 */
$wgObjectCaches = array(
    CACHE_NONE => array('class' => 'EmptyBagOStuff'),
    CACHE_DBA => array('class' => 'DBABagOStuff'),
    CACHE_ANYTHING => array('factory' => 'ObjectCache::newAnything'),
    CACHE_ACCEL => array('factory' => 'ObjectCache::newAccelerator'),
    CACHE_MEMCACHED => array('factory' => 'ObjectCache::newMemcached'),
    'apc' => array('class' => 'APCBagOStuff'),
    'xcache' => array('class' => 'XCacheBagOStuff'),
    'wincache' => array('class' => 'WinCacheBagOStuff'),
    'memcached-php' => array('class' => 'MemcachedPhpBagOStuff'),
    'hash' => array('class' => 'HashBagOStuff'),
);

/**
 * The expiry time for the parser cache, in seconds. The default is 86.4k
 * seconds, otherwise known as a day.
 */
$wgParserCacheExpireTime = 86400;

/**
 * Select which DBA handler <http://www.php.net/manual/en/dba.requirements.php> to use as CACHE_DBA backend
 */
$wgDBAhandler = 'db3';

/**
 * Store sessions in MemCached. This can be useful to improve performance, or to
 * avoid the locking behaviour of PHP's default session handler, which tends to
 * prevent multiple requests for the same user from acting concurrently.
 */
$wgSessionsInMemcached = false;

/**
 * This is used for setting php's session.save_handler. In practice, you will
 * almost never need to change this ever. Other options might be 'user' or
 * 'session_mysql.' Setting to null skips setting this entirely (which might be
 * useful if you're doing cross-application sessions, see bug 11381)
 */
$wgSessionHandler = null;

/** If enabled, will send MemCached debugging information to $wgDebugLogFile */
$wgMemCachedDebug = false;

/** The list of MemCached servers and port numbers */
$wgMemCachedServers = array('127.0.0.1:11000');

/**
 * Use persistent connections to MemCached, which are shared across multiple
 * requests.
 */
$wgMemCachedPersistent = false;

/**
 * Read/write timeout for MemCached server communication, in microseconds.
 */
$wgMemCachedTimeout = 500000;

/**
 * Set this to true to make a local copy of the message cache, for use in
 * addition to memcached. The files will be put in $wgCacheDirectory.
 */
$wgUseLocalMessageCache = false;

/**
 * Defines format of local cache
 * true - Serialized object
 * false - PHP source file (Warning - security risk)
 */
$wgLocalMessageCacheSerialized = true;

/**
 * Instead of caching everything, keep track which messages are requested and
 * load only most used messages. This only makes sense if there is lots of
 * interface messages customised in the wiki (like hundreds in many languages).
 */
$wgAdaptiveMessageCache = false;

/**
 * Localisation cache configuration. Associative array with keys:
 *     class:       The class to use. May be overridden by extensions.
 *
 *     store:       The location to store cache data. May be 'files', 'db' or
 *                  'detect'. If set to "files", data will be in CDB files. If set
 *                  to "db", data will be stored to the database. If set to
 *                  "detect", files will be used if $wgCacheDirectory is set,
 *                  otherwise the database will be used.
 *
 *     storeClass:  The class name for the underlying storage. If set to a class
 *                  name, it overrides the "store" setting.
 *
 *     storeDirectory:  If the store class puts its data in files, this is the
 *                      directory it will use. If this is false, $wgCacheDirectory
 *                      will be used.
 *
 *     manualRecache:   Set this to true to disable cache updates on web requests.
 *                      Use maintenance/rebuildLocalisationCache.php instead.
 */
$wgLocalisationCacheConf = array(
    'class' => 'LocalisationCache',
    'store' => 'detect',
    'storeClass' => false,
    'storeDirectory' => false,
    'manualRecache' => false,
);

/** Allow client-side caching of pages */
$wgCachePages = true;

/**
 * Set this to current time to invalidate all prior cached pages. Affects both
 * client- and server-side caching.
 * You can get the current date on your server by using the command:
 *   date +%Y%m%d%H%M%S
 */
$wgCacheEpoch = '20030516000000';

/**
 * Bump this number when changing the global style sheets and JavaScript.
 * It should be appended in the query string of static CSS and JS includes,
 * to ensure that client-side caches do not keep obsolete copies of global
 * styles.
 */
$wgStyleVersion = '303';

/**
 * This will cache static pages for non-logged-in users to reduce
 * database traffic on public sites.
 * Must set $wgShowIPinHeader = false
 * ResourceLoader requests to default language and skins are cached
 * as well as single module requests.
 */
$wgUseFileCache = false;

/**
 * Directory where the cached page will be saved.
 * Will default to "{$wgUploadDirectory}/cache" in Setup.php
 */
$wgFileCacheDirectory = false;

/**
 * Depth of the subdirectory hierarchy to be created under
 * $wgFileCacheDirectory.  The subdirectories will be named based on
 * the MD5 hash of the title.  A value of 0 means all cache files will
 * be put directly into the main file cache directory.
 */
$wgFileCacheDepth = 2;

/**
 * Keep parsed pages in a cache (objectcache table or memcached)
 * to speed up output of the same page viewed by another user with the
 * same options.
 *
 * This can provide a significant speedup for medium to large pages,
 * so you probably want to keep it on. Extensions that conflict with the
 * parser cache should disable the cache on a per-page basis instead.
 */
$wgEnableParserCache = true;

/**
 * Append a configured value to the parser cache and the sitenotice key so
 * that they can be kept separate for some class of activity.
 */
$wgRenderHashAppend = '';

/**
 * If on, the sidebar navigation links are cached for users with the
 * current language set. This can save a touch of load on a busy site
 * by shaving off extra message lookups.
 *
 * However it is also fragile: changing the site configuration, or
 * having a variable $wgArticlePath, can produce broken links that
 * don't update as expected.
 */
$wgEnableSidebarCache = false;

/**
 * Expiry time for the sidebar cache, in seconds
 */
$wgSidebarCacheExpiry = 86400;

/**
 * When using the file cache, we can store the cached HTML gzipped to save disk
 * space. Pages will then also be served compressed to clients that support it.
 * THIS IS NOT COMPATIBLE with ob_gzhandler which is now enabled if supported in
 * the default LocalSettings.php! If you enable this, remove that setting first.
 *
 * Requires zlib support enabled in PHP.
 */
$wgUseGzip = false;

/**
 * Whether MediaWiki should send an ETag header. Seems to cause
 * broken behavior with Squid 2.6, see bug 7098.
 */
$wgUseETag = false;

/** Clock skew or the one-second resolution of time() can occasionally cause cache
 * problems when the user requests two pages within a short period of time. This
 * variable adds a given number of seconds to vulnerable timestamps, thereby giving
 * a grace period.
 */
$wgClockSkewFudge = 5;

/**
 * Invalidate various caches when LocalSettings.php changes. This is equivalent
 * to setting $wgCacheEpoch to the modification time of LocalSettings.php, as
 * was previously done in the default LocalSettings.php file.
 *
 * On high-traffic wikis, this should be set to false, to avoid the need to
 * check the file modification time, and to avoid the performance impact of
 * unnecessary cache invalidations.
 */
$wgInvalidateCacheOnLocalSettingsChange = true;

/** @} */ # end of cache settings

/* * ********************************************************************* *//**
 * @name   HTTP proxy (Squid) settings
 *
 * Many of these settings apply to any HTTP proxy used in front of MediaWiki,
 * although they are referred to as Squid settings for historical reasons.
 *
 * Achieving a high hit ratio with an HTTP proxy requires special
 * configuration. See http://www.mediawiki.org/wiki/Manual:Squid_caching for
 * more details.
 *
 * @{
 */
/**
 * Enable/disable Squid.
 * See http://www.mediawiki.org/wiki/Manual:Squid_caching
 */
$wgUseSquid = false;

/** If you run Squid3 with ESI support, enable this (default:false): */
$wgUseESI = false;

/** Send X-Vary-Options header for better caching (requires patched Squid) */
$wgUseXVO = false;

/** Add X-Forwarded-Proto to the Vary and X-Vary-Options headers for API
 * requests and RSS/Atom feeds. Use this if you have an SSL termination setup
 * and need to split the cache between HTTP and HTTPS for API requests,
 * feed requests and HTTP redirect responses in order to prevent cache
 * pollution. This does not affect 'normal' requests to index.php other than
 * HTTP redirects.
 */
$wgVaryOnXFP = false;

/**
 * Internal server name as known to Squid, if different. Example:
 * <code>
 * $wgInternalServer = 'http://yourinternal.tld:8000';
 * </code>
 */
$wgInternalServer = false;

/**
 * Cache timeout for the squid, will be sent as s-maxage (without ESI) or
 * Surrogate-Control (with ESI). Without ESI, you should strip out s-maxage in
 * the Squid config. 18000 seconds = 5 hours, more cache hits with 2678400 = 31
 * days
 */
$wgSquidMaxage = 18000;

/**
 * Default maximum age for raw CSS/JS accesses
 */
$wgForcedRawSMaxage = 300;

/**
 * List of proxy servers to purge on changes; default port is 80. Use IP addresses.
 *
 * When MediaWiki is running behind a proxy, it will trust X-Forwarded-For
 * headers sent/modified from these proxies when obtaining the remote IP address
 *
 * For a list of trusted servers which *aren't* purged, see $wgSquidServersNoPurge.
 */
$wgSquidServers = array();

/**
 * As above, except these servers aren't purged on page changes; use to set a
 * list of trusted proxies, etc.
 */
$wgSquidServersNoPurge = array();

/** Maximum number of titles to purge in any one client operation */
$wgMaxSquidPurgeTitles = 400;

/**
 * HTCP multicast address. Set this to a multicast IP address to enable HTCP.
 *
 * Note that MediaWiki uses the old non-RFC compliant HTCP format, which was
 * present in the earliest Squid implementations of the protocol.
 */
$wgHTCPMulticastAddress = false;

/**
 * HTCP multicast port.
 * @see $wgHTCPMulticastAddress
 */
$wgHTCPPort = 4827;

/**
 * HTCP multicast TTL.
 * @see $wgHTCPMulticastAddress
 */
$wgHTCPMulticastTTL = 1;

/** Should forwarded Private IPs be accepted? */
$wgUsePrivateIPs = false;

/** @} */ # end of HTTP proxy settings

/* * ********************************************************************* *//**
 * @name   Language, regional and character encoding settings
 * @{
 */
/** Site language code, should be one of ./languages/Language(.*).php */
$wgLanguageCode = 'en';

/**
 * Some languages need different word forms, usually for different cases.
 * Used in Language::convertGrammar(). Example:
 *
 * <code>
 * $wgGrammarForms['en']['genitive']['car'] = 'car\'s';
 * </code>
 */
$wgGrammarForms = array();

/** Treat language links as magic connectors, not inline links */
$wgInterwikiMagic = true;

/** Hide interlanguage links from the sidebar */
$wgHideInterlanguageLinks = false;

/** List of language names or overrides for default names in Names.php */
$wgExtraLanguageNames = array();

/**
 * List of language codes that don't correspond to an actual language.
 * These codes are mostly leftoffs from renames, or other legacy things.
 * This array makes them not appear as a selectable language on the installer,
 * and excludes them when running the transstat.php script.
 */
$wgDummyLanguageCodes = array(
    'als' => 'gsw',
    'bat-smg' => 'sgs',
    'be-x-old' => 'be-tarask',
    'bh' => 'bho',
    'fiu-vro' => 'vro',
    'lol' => 'lol', # Used for In Context Translations
    'no' => 'nb',
    'qqq' => 'qqq', # Used for message documentation.
    'qqx' => 'qqx', # Used for viewing message keys.
    'roa-rup' => 'rup',
    'simple' => 'en',
    'zh-classical' => 'lzh',
    'zh-min-nan' => 'nan',
    'zh-yue' => 'yue',
);

/**
 * Character set for use in the article edit box. Language-specific encodings
 * may be defined.
 *
 * This historic feature is one of the first that was added by former MediaWiki
 * team leader Brion Vibber, and is used to support the Esperanto x-system.
 */
$wgEditEncoding = '';

/**
 * Set this to true to replace Arabic presentation forms with their standard
 * forms in the U+0600-U+06FF block. This only works if $wgLanguageCode is
 * set to "ar".
 *
 * Note that pages with titles containing presentation forms will become
 * inaccessible, run maintenance/cleanupTitles.php to fix this.
 */
$wgFixArabicUnicode = true;

/**
 * Set this to true to replace ZWJ-based chillu sequences in Malayalam text
 * with their Unicode 5.1 equivalents. This only works if $wgLanguageCode is
 * set to "ml". Note that some clients (even new clients as of 2010) do not
 * support these characters.
 *
 * If you enable this on an existing wiki, run maintenance/cleanupTitles.php to
 * fix any ZWJ sequences in existing page titles.
 */
$wgFixMalayalamUnicode = true;

/**
 * Set this to always convert certain Unicode sequences to modern ones
 * regardless of the content language. This has a small performance
 * impact.
 *
 * See $wgFixArabicUnicode and $wgFixMalayalamUnicode for conversion
 * details.
 *
 * @since 1.17
 */
$wgAllUnicodeFixes = false;

/**
 * Set this to eg 'ISO-8859-1' to perform character set conversion when
 * loading old revisions not marked with "utf-8" flag. Use this when
 * converting a wiki from MediaWiki 1.4 or earlier to UTF-8 without the
 * burdensome mass conversion of old text data.
 *
 * NOTE! This DOES NOT touch any fields other than old_text.Titles, comments,
 * user names, etc still must be converted en masse in the database before
 * continuing as a UTF-8 wiki.
 */
$wgLegacyEncoding = false;

/**
 * Browser Blacklist for unicode non compliant browsers. Contains a list of
 * regexps : "/regexp/"  matching problematic browsers. These browsers will
 * be served encoded unicode in the edit box instead of real unicode.
 */
$wgBrowserBlackList = array(
    /**
     * Netscape 2-4 detection
     * The minor version may contain strings such as "Gold" or "SGoldC-SGI"
     * Lots of non-netscape user agents have "compatible", so it's useful to check for that
     * with a negative assertion. The [UIN] identifier specifies the level of security
     * in a Netscape/Mozilla browser, checking for it rules out a number of fakers.
     * The language string is unreliable, it is missing on NS4 Mac.
     *
     * Reference: http://www.psychedelix.com/agents/index.shtml
     */
    '/^Mozilla\/2\.[^ ]+ [^(]*?\((?!compatible).*; [UIN]/',
    '/^Mozilla\/3\.[^ ]+ [^(]*?\((?!compatible).*; [UIN]/',
    '/^Mozilla\/4\.[^ ]+ [^(]*?\((?!compatible).*; [UIN]/',
    /**
     * MSIE on Mac OS 9 is teh sux0r, converts þ to <thorn>, ð to <eth>, Þ to <THORN> and Ð to <ETH>
     *
     * Known useragents:
     * - Mozilla/4.0 (compatible; MSIE 5.0; Mac_PowerPC)
     * - Mozilla/4.0 (compatible; MSIE 5.15; Mac_PowerPC)
     * - Mozilla/4.0 (compatible; MSIE 5.23; Mac_PowerPC)
     * - [...]
     *
     * @link http://en.wikipedia.org/w/index.php?title=User%3A%C6var_Arnfj%F6r%F0_Bjarmason%2Ftestme&diff=12356041&oldid=12355864
     * @link http://en.wikipedia.org/wiki/Template%3AOS9
     */
    '/^Mozilla\/4\.0 \(compatible; MSIE \d+\.\d+; Mac_PowerPC\)/',
    /**
     * Google wireless transcoder, seems to eat a lot of chars alive
     * http://it.wikipedia.org/w/index.php?title=Luciano_Ligabue&diff=prev&oldid=8857361
     */
    '/^Mozilla\/4\.0 \(compatible; MSIE 6.0; Windows NT 5.0; Google Wireless Transcoder;\)/'
);

/**
 * If set to true, the MediaWiki 1.4 to 1.5 schema conversion will
 * create stub reference rows in the text table instead of copying
 * the full text of all current entries from 'cur' to 'text'.
 *
 * This will speed up the conversion step for large sites, but
 * requires that the cur table be kept around for those revisions
 * to remain viewable.
 *
 * maintenance/migrateCurStubs.php can be used to complete the
 * migration in the background once the wiki is back online.
 *
 * This option affects the updaters *only*. Any present cur stub
 * revisions will be readable at runtime regardless of this setting.
 */
$wgLegacySchemaConversion = false;

/**
 * Enable to allow rewriting dates in page text.
 * DOES NOT FORMAT CORRECTLY FOR MOST LANGUAGES.
 */
$wgUseDynamicDates = false;
/**
 * Enable dates like 'May 12' instead of '12 May', this only takes effect if
 * the interface is set to English.
 */
$wgAmericanDates = false;
/**
 * For Hindi and Arabic use local numerals instead of Western style (0-9)
 * numerals in interface.
 */
$wgTranslateNumerals = true;

/**
 * Translation using MediaWiki: namespace.
 * Interface messages will be loaded from the database.
 */
$wgUseDatabaseMessages = true;

/**
 * Expiry time for the message cache key
 */
$wgMsgCacheExpiry = 86400;

/**
 * Maximum entry size in the message cache, in bytes
 */
$wgMaxMsgCacheEntrySize = 10000;

/** Whether to enable language variant conversion. */
$wgDisableLangConversion = false;

/** Whether to enable language variant conversion for links. */
$wgDisableTitleConversion = false;

/** Whether to enable cononical language links in meta data. */
$wgCanonicalLanguageLinks = true;

/** Default variant code, if false, the default will be the language code */
$wgDefaultLanguageVariant = false;

/**
 * Disabled variants array of language variant conversion. Example:
 * <code>
 *  $wgDisabledVariants[] = 'zh-mo';
 *  $wgDisabledVariants[] = 'zh-my';
 * </code>
 *
 * or:
 *
 * <code>
 *  $wgDisabledVariants = array('zh-mo', 'zh-my');
 * </code>
 */
$wgDisabledVariants = array();

/**
 * Like $wgArticlePath, but on multi-variant wikis, this provides a
 * path format that describes which parts of the URL contain the
 * language variant.  For Example:
 *
 *   $wgLanguageCode = 'sr';
 *   $wgVariantArticlePath = '/$2/$1';
 *   $wgArticlePath = '/wiki/$1';
 *
 * A link to /wiki/ would be redirected to /sr/Главна_страна
 *
 * It is important that $wgArticlePath not overlap with possible values
 * of $wgVariantArticlePath.
 */
$wgVariantArticlePath = false;

/**
 * Show a bar of language selection links in the user login and user
 * registration forms; edit the "loginlanguagelinks" message to
 * customise these.
 */
$wgLoginLanguageSelector = false;

/**
 * When translating messages with wfMsg(), it is not always clear what should
 * be considered UI messages and what should be content messages.
 *
 * For example, for the English Wikipedia, there should be only one 'mainpage',
 * so when getting the link for 'mainpage', we should treat it as site content
 * and call wfMsgForContent(), but for rendering the text of the link, we call
 * wfMsg(). The code behaves this way by default. However, sites like the
 * Wikimedia Commons do offer different versions of 'mainpage' and the like for
 * different languages. This array provides a way to override the default
 * behavior. For example, to allow language-specific main page and community
 * portal, set
 *
 * $wgForceUIMsgAsContentMsg = array( 'mainpage', 'portal-url' );
 */
$wgForceUIMsgAsContentMsg = array();

/**
 * Fake out the timezone that the server thinks it's in. This will be used for
 * date display and not for what's stored in the DB. Leave to null to retain
 * your server's OS-based timezone value.
 *
 * This variable is currently used only for signature formatting and for local
 * time/date parser variables ({{LOCALTIME}} etc.)
 *
 * Timezones can be translated by editing MediaWiki messages of type
 * timezone-nameinlowercase like timezone-utc.
 *
 * Examples:
 * <code>
 * $wgLocaltimezone = 'GMT';
 * $wgLocaltimezone = 'PST8PDT';
 * $wgLocaltimezone = 'Europe/Sweden';
 * $wgLocaltimezone = 'CET';
 * </code>
 */
$wgLocaltimezone = null;

/**
 * Set an offset from UTC in minutes to use for the default timezone setting
 * for anonymous users and new user accounts.
 *
 * This setting is used for most date/time displays in the software, and is
 * overrideable in user preferences. It is *not* used for signature timestamps.
 *
 * By default, this will be set to match $wgLocaltimezone.
 */
$wgLocalTZoffset = null;

/**
 * If set to true, this will roll back a few bug fixes introduced in 1.19,
 * emulating the 1.18 behaviour, to avoid introducing bug 34832. In 1.19,
 * language variant conversion is disabled in interface messages. Setting this
 * to true re-enables it.
 *
 * This variable should be removed (implicitly false) in 1.20 or earlier.
 */
$wgBug34832TransitionalRollback = true;


/** @} */ # End of language/charset settings

/* * ********************************************************************** *//**
 * @name   Output format and skin settings
 * @{
 */
/** The default Content-Type header. */
$wgMimeType = 'text/html';

/**
 * The content type used in script tags.  This is mostly going to be ignored if
 * $wgHtml5 is true, at least for actual HTML output, since HTML5 doesn't
 * require a MIME type for JavaScript or CSS (those are the default script and
 * style languages).
 */
$wgJsMimeType = 'text/javascript';

/**
 * The HTML document type.  Ignored if $wgHtml5 is true, since <!DOCTYPE html>
 * doesn't actually have a doctype part to put this variable's contents in.
 */
$wgDocType = '-//W3C//DTD XHTML 1.0 Transitional//EN';

/**
 * The URL of the document type declaration.  Ignored if $wgHtml5 is true,
 * since HTML5 has no DTD, and <!DOCTYPE html> doesn't actually have a DTD part
 * to put this variable's contents in.
 */
$wgDTD = 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd';

/**
 * The default xmlns attribute.  Ignored if $wgHtml5 is true (or it's supposed
 * to be), since we don't currently support XHTML5, and in HTML5 (i.e., served
 * as text/html) the attribute has no effect, so why bother?
 */
$wgXhtmlDefaultNamespace = 'http://www.w3.org/1999/xhtml';

/**
 * Should we output an HTML5 doctype?  If false, use XHTML 1.0 Transitional
 * instead, and disable HTML5 features.  This may eventually be removed and set
 * to always true.  If it's true, a number of other settings will be irrelevant
 * and have no effect.
 */
$wgHtml5 = true;

/**
 * Defines the value of the version attribute in the &lt;html&gt; tag, if any.
 * This is ignored if $wgHtml5 is false.  If $wgAllowRdfaAttributes and
 * $wgHtml5 are both true, and this evaluates to boolean false (like if it's
 * left at the default null value), it will be auto-initialized to the correct
 * value for RDFa+HTML5.  As such, you should have no reason to ever actually
 * set this to anything.
 */
$wgHtml5Version = null;

/**
 * Enabled RDFa attributes for use in wikitext.
 * NOTE: Interaction with HTML5 is somewhat underspecified.
 */
$wgAllowRdfaAttributes = false;

/**
 * Enabled HTML5 microdata attributes for use in wikitext, if $wgHtml5 is also true.
 */
$wgAllowMicrodataAttributes = false;

/**
 * Cleanup as much presentational html like valign -> css vertical-align as we can
 */
$wgCleanupPresentationalAttributes = true;

/**
 * Permit other namespaces in addition to the w3.org default.
 * Use the prefix for the key and the namespace for the value. For
 * example:
 * $wgXhtmlNamespaces['svg'] = 'http://www.w3.org/2000/svg';
 * Normally we wouldn't have to define this in the root <html>
 * element, but IE needs it there in some circumstances.
 *
 * This is ignored if $wgHtml5 is true, for the same reason as
 * $wgXhtmlDefaultNamespace.
 */
$wgXhtmlNamespaces = array();

/**
 * Show IP address, for non-logged in users. It's necessary to switch this off
 * for some forms of caching.
 * Will disable file cache.
 */
$wgShowIPinHeader = true;

/**
 * Site notice shown at the top of each page
 *
 * MediaWiki:Sitenotice page, which will override this. You can also
 * provide a separate message for logged-out users using the
 * MediaWiki:Anonnotice page.
 */
$wgSiteNotice = '';

/**
 * A subtitle to add to the tagline, for skins that have it/
 */
$wgExtraSubtitle = '';

/**
 * Validate the overall output using tidy and refuse
 * to display the page if it's not valid.
 */
$wgValidateAllHtml = false;

/**
 * Default skin, for new users and anonymous visitors. Registered users may
 * change this to any one of the other available skins in their preferences.
 * This has to be completely lowercase; see the "skins" directory for the list
 * of available skins.
 */
$wgDefaultSkin = 'oasis';

/**
 * Specify the name of a skin that should not be presented in the list of
 * available skins.  Use for blacklisting a skin which you do not want to
 * remove from the .../skins/ directory
 */
$wgSkipSkins = array();

/**
 * Optionally, we can specify a stylesheet to use for media="handheld".
 * This is recognized by some, but not all, handheld/mobile/PDA browsers.
 * If left empty, compliant handheld browsers won't pick up the skin
 * stylesheet, which is specified for 'screen' media.
 *
 * Can be a complete URL, base-relative path, or $wgStylePath-relative path.
 *
 * Will also be switched in when 'handheld=yes' is added to the URL, like
 * the 'printable=yes' mode for print media.
 */
$wgHandheldStyle = false;

/**
 * If set, 'screen' and 'handheld' media specifiers for stylesheets are
 * transformed such that they apply to the iPhone/iPod Touch Mobile Safari,
 * which doesn't recognize 'handheld' but does support media queries on its
 * screen size.
 *
 * Consider only using this if you have a *really good* handheld stylesheet,
 * as iPhone users won't have any way to disable it and use the "grown-up"
 * styles instead.
 */
$wgHandheldForIPhone = false;

/**
 * Allow user Javascript page?
 * This enables a lot of neat customizations, but may
 * increase security risk to users and server load.
 */
$wgAllowUserJs = false;

/**
 * Allow user Cascading Style Sheets (CSS)?
 * This enables a lot of neat customizations, but may
 * increase security risk to users and server load.
 */
$wgAllowUserCss = false;

/**
 * Allow user-preferences implemented in CSS?
 * This allows users to customise the site appearance to a greater
 * degree; disabling it will improve page load times.
 */
$wgAllowUserCssPrefs = true;

/** Use the site's Javascript page? */
$wgUseSiteJs = true;

/** Use the site's Cascading Style Sheets (CSS)? */
$wgUseSiteCss = true;

/**
 * Break out of framesets. This can be used to prevent clickjacking attacks,
 * or to prevent external sites from framing your site with ads.
 */
$wgBreakFrames = false;

/**
 * The X-Frame-Options header to send on pages sensitive to clickjacking
 * attacks, such as edit pages. This prevents those pages from being displayed
 * in a frame or iframe. The options are:
 *
 *   - 'DENY': Do not allow framing. This is recommended for most wikis.
 *
 *   - 'SAMEORIGIN': Allow framing by pages on the same domain. This can be used
 *         to allow framing within a trusted domain. This is insecure if there
 *         is a page on the same domain which allows framing of arbitrary URLs.
 *
 *   - false: Allow all framing. This opens up the wiki to XSS attacks and thus
 *         full compromise of local user accounts. Private wikis behind a
 *         corporate firewall are especially vulnerable. This is not
 *         recommended.
 *
 * For extra safety, set $wgBreakFrames = true, to prevent framing on all pages,
 * not just edit pages.
 */
$wgEditPageFrameOptions = 'DENY';

/**
 * Disallow framing of API pages directly, by setting the X-Frame-Options
 * header. Since the API returns CSRF tokens, allowing the results to be
 * framed can compromise your user's account security.
 * Options are:
 *   - 'DENY': Do not allow framing. This is recommended for most wikis.
 *   - 'SAMEORIGIN': Allow framing by pages on the same domain.
 *   - false: Allow all framing.
 */
$wgApiFrameOptions = 'DENY';

/**
 * Disable output compression (enabled by default if zlib is available)
 */
$wgDisableOutputCompression = false;

/**
 * Should we allow a broader set of characters in id attributes, per HTML5?  If
 * not, use only HTML 4-compatible IDs.  This option is for testing -- when the
 * functionality is ready, it will be on by default with no option.
 *
 * Currently this appears to work fine in all browsers, but it's disabled by
 * default because it normalizes id's a bit too aggressively, breaking preexisting
 * content (particularly Cite).  See bug 27733, bug 27694, bug 27474.
 */
$wgExperimentalHtmlIds = false;

/**
 * Abstract list of footer icons for skins in place of old copyrightico and poweredbyico code
 * You can add new icons to the built in copyright or poweredby, or you can create
 * a new block. Though note that you may need to add some custom css to get good styling
 * of new blocks in monobook. vector and modern should work without any special css.
 *
 * $wgFooterIcons itself is a key/value array.
 * The key is the name of a block that the icons will be wrapped in. The final id varies
 * by skin; Monobook and Vector will turn poweredby into f-poweredbyico while Modern
 * turns it into mw_poweredby.
 * The value is either key/value array of icons or a string.
 * In the key/value array the key may or may not be used by the skin but it can
 * be used to find the icon and unset it or change the icon if needed.
 * This is useful for disabling icons that are set by extensions.
 * The value should be either a string or an array. If it is a string it will be output
 * directly as html, however some skins may choose to ignore it. An array is the preferred format
 * for the icon, the following keys are used:
 *   src: An absolute url to the image to use for the icon, this is recommended
 *        but not required, however some skins will ignore icons without an image
 *   url: The url to use in the <a> arround the text or icon, if not set an <a> will not be outputted
 *   alt: This is the text form of the icon, it will be displayed without an image in
 *        skins like Modern or if src is not set, and will otherwise be used as
 *        the alt="" for the image. This key is required.
 *   width and height: If the icon specified by src is not of the standard size
 *                     you can specify the size of image to use with these keys.
 *                     Otherwise they will default to the standard 88x31.
 */
$wgFooterIcons = array(
    "copyright" => array(
        "copyright" => array(), // placeholder for the built in copyright icon
    ),
    "poweredby" => array(
        "mediawiki" => array(
            "src" => null, // Defaults to "$wgStylePath/common/images/poweredby_mediawiki_88x31.png"
            "url" => "//www.mediawiki.org/",
            "alt" => "Powered by MediaWiki",
        )
    ),
);

/**
 * Login / create account link behavior when it's possible for anonymous users to create an account
 * true = use a combined login / create account link
 * false = split login and create account into two separate links
 */
$wgUseCombinedLoginLink = true;

/**
 * Display user edit counts in various prominent places.
 */
$wgEdititis = false;

/**
 * Better directionality support (bug 6100 and related).
 * Removed in 1.18, still kept here for LiquidThreads backwards compatibility.
 *
 * @deprecated since 1.18
 */
$wgBetterDirectionality = true;

/**
 * Some web hosts attempt to rewrite all responses with a 404 (not found)
 * status code, mangling or hiding MediaWiki's output. If you are using such a
 * host, you should start looking for a better one. While you're doing that,
 * set this to false to convert some of MediaWiki's 404 responses to 200 so
 * that the generated error pages can be seen.
 *
 * In cases where for technical reasons it is more important for MediaWiki to
 * send the correct status code than for the body to be transmitted intact,
 * this configuration variable is ignored.
 */
$wgSend404Code = true;

/** @} */ # End of output format settings }

/* * ********************************************************************** *//**
 * @name   Resource loader settings
 * @{
 */
/**
 * Client-side resource modules. Extensions should add their module definitions
 * here.
 *
 * Example:
 *   $wgResourceModules['ext.myExtension'] = array(
 *      'scripts' => 'myExtension.js',
 *      'styles' => 'myExtension.css',
 *      'dependencies' => array( 'jquery.cookie', 'jquery.tabIndex' ),
 *      'localBasePath' => dirname( __FILE__ ),
 *      'remoteExtPath' => 'MyExtension',
 *   );
 */
$wgResourceModules = array();

/**
 * Extensions should register foreign module sources here. 'local' is a
 * built-in source that is not in this array, but defined by
 * ResourceLoader::__construct() so that it cannot be unset.
 *
 * Example:
 *   $wgResourceLoaderSources['foo'] = array(
 *       'loadScript' => 'http://example.org/w/load.php',
 *       'apiScript' => 'http://example.org/w/api.php'
 *   );
 */
$wgResourceLoaderSources = array();

/**
 * Default 'remoteBasePath' value for resource loader modules.
 * If not set, then $wgScriptPath will be used as a fallback.
 */
$wgResourceBasePath = null;

/**
 * Maximum time in seconds to cache resources served by the resource loader
 */
$wgResourceLoaderMaxage = array(
    'versioned' => array(
        // Squid/Varnish but also any other public proxy cache between the client and MediaWiki
        'server' => 30 * 24 * 60 * 60, // 30 days
        // On the client side (e.g. in the browser cache).
        'client' => 30 * 24 * 60 * 60, // 30 days
    ),
    'unversioned' => array(
        'server' => 5 * 60, // 5 minutes
        'client' => 5 * 60, // 5 minutes
    ),
);

/**
 * The default debug mode (on/off) for of ResourceLoader requests. This will still
 * be overridden when the debug URL parameter is used.
 */
$wgResourceLoaderDebug = false;

/**
 * Enable embedding of certain resources using Edge Side Includes. This will
 * improve performance but only works if there is something in front of the
 * web server (e..g a Squid or Varnish server) configured to process the ESI.
 */
$wgResourceLoaderUseESI = false;

/**
 * Put each statement on its own line when minifying JavaScript. This makes
 * debugging in non-debug mode a bit easier.
 */
$wgResourceLoaderMinifierStatementsOnOwnLine = false;

/**
 * Maximum line length when minifying JavaScript. This is not a hard maximum:
 * the minifier will try not to produce lines longer than this, but may be
 * forced to do so in certain cases.
 */
$wgResourceLoaderMinifierMaxLineLength = 1000;

/**
 * Whether to include the mediawiki.legacy JS library (old wikibits.js), and its
 * dependencies
 */
$wgIncludeLegacyJavaScript = true;

/**
 * Whether to preload the mediawiki.util module as blocking module in the top queue.
 * Before MediaWiki 1.19, modules used to load slower/less asynchronous which allowed
 * modules to lack dependencies on 'popular' modules that were likely loaded already.
 * This setting is to aid scripts during migration by providing mediawiki.util
 * unconditionally (which was the most commonly missed dependency).
 * It doesn't cover all missing dependencies obviously but should fix most of them.
 * This should be removed at some point after site/user scripts have been fixed.
 * Enable this if your wiki has a large amount of user/site scripts that are lacking
 * dependencies.
 */
$wgPreloadJavaScriptMwUtil = false;

/**
 * Whether or not to assing configuration variables to the global window object.
 * If this is set to false, old code using deprecated variables like:
 * " if ( window.wgRestrictionEdit ) ..."
 * or:
 * " if ( wgIsArticle ) ..."
 * will no longer work and needs to use mw.config instead. For example:
 * " if ( mw.config.exists('wgRestrictionEdit') )"
 * or
 * " if ( mw.config.get('wgIsArticle') )".
 */
$wgLegacyJavaScriptGlobals = true;

/**
 * If set to a positive number, ResourceLoader will not generate URLs whose
 * query string is more than this many characters long, and will instead use
 * multiple requests with shorter query strings. This degrades performance,
 * but may be needed if your web server has a low (less than, say 1024)
 * query string length limit or a low value for suhosin.get.max_value_length
 * that you can't increase.
 *
 * If set to a negative number, ResourceLoader will assume there is no query
 * string length limit.
 */
$wgResourceLoaderMaxQueryLength = -1;

/**
 * If set to true, JavaScript modules loaded from wiki pages will be parsed prior
 * to minification to validate it.
 *
 * Parse errors will result in a JS exception being thrown during module load,
 * which avoids breaking other modules loaded in the same request.
 */
$wgResourceLoaderValidateJS = true;

/**
 * If set to true, statically-sourced (file-backed) JavaScript resources will
 * be parsed for validity before being bundled up into ResourceLoader modules.
 *
 * This can be helpful for development by providing better error messages in
 * default (non-debug) mode, but JavaScript parsing is slow and memory hungry
 * and may fail on large pre-bundled frameworks.
 */
$wgResourceLoaderValidateStaticJS = false;

/**
 * If set to true, asynchronous loading of bottom-queue scripts in the <head>
 * will be enabled. This is an experimental feature that's supposed to make
 * JavaScript load faster.
 */
$wgResourceLoaderExperimentalAsyncLoading = false;

/**
 * Whether to allow site-wide CSS (MediaWiki:Common.css and friends) on
 * restricted pages like Special:UserLogin or Special:Preferences where
 * JavaScript is disabled for security reasons. As it is possible to
 * execute JavaScript through CSS, setting this to true opens up a
 * potential security hole. Some sites may "skin" their wiki by using
 * site-wide CSS, causing restricted pages to look unstyled and different
 * from the rest of the site.
 *
 * @since 1.25
 */
$wgAllowSiteCSSOnRestrictedPages = false;

/**
 * When OutputHandler is used, mangle any output that contains
 * <cross-domain-policy>. Without this, an attacker can send their own
 * cross-domain policy unless it is prevented by the crossdomain.xml file at
 * the domain root.
 */
$wgMangleFlashPolicy = true;

/** @} */ # End of resource loader settings }


/* * ********************************************************************** *//**
 * @name   Page title and interwiki link settings
 * @{
 */
/**
 * Name of the project namespace. If left set to false, $wgSitename will be
 * used instead.
 */
$wgMetaNamespace = false;

/**
 * Name of the project talk namespace.
 *
 * Normally you can ignore this and it will be something like
 * $wgMetaNamespace . "_talk". In some languages, you may want to set this
 * manually for grammatical reasons.
 */
$wgMetaNamespaceTalk = false;

/**
 * Additional namespaces. If the namespaces defined in Language.php and
 * Namespace.php are insufficient, you can create new ones here, for example,
 * to import Help files in other languages. You can also override the namespace
 * names of existing namespaces. Extensions developers should use
 * $wgCanonicalNamespaceNames.
 *
 * PLEASE  NOTE: Once you delete a namespace, the pages in that namespace will
 * no longer be accessible. If you rename it, then you can access them through
 * the new namespace name.
 *
 * Custom namespaces should start at 100 to avoid conflicting with standard
 * namespaces, and should always follow the even/odd main/talk pattern.
 */
# $wgExtraNamespaces = array(
#     100 => "Hilfe",
#     101 => "Hilfe_Diskussion",
#     102 => "Aide",
#     103 => "Discussion_Aide"
# );
$wgExtraNamespaces = array();

/**
 * Same as above, but for namespaces with gender distinction.
 * Note: the default form for the namespace should also be set
 * using $wgExtraNamespaces for the same index.
 * @since 1.18
 */
$wgExtraGenderNamespaces = array();

/**
 * Namespace aliases
 * These are alternate names for the primary localised namespace names, which
 * are defined by $wgExtraNamespaces and the language file. If a page is
 * requested with such a prefix, the request will be redirected to the primary
 * name.
 *
 * Set this to a map from namespace names to IDs.
 * Example:
 *    $wgNamespaceAliases = array(
 *        'Wikipedian' => NS_USER,
 *        'Help' => 100,
 *    );
 */
$wgNamespaceAliases = array();

/**
 * Allowed title characters -- regex character class
 * Don't change this unless you know what you're doing
 *
 * Problematic punctuation:
 *   -  []{}|#    Are needed for link syntax, never enable these
 *   -  <>        Causes problems with HTML escaping, don't use
 *   -  %         Enabled by default, minor problems with path to query rewrite rules, see below
 *   -  +         Enabled by default, but doesn't work with path to query rewrite rules, corrupted by apache
 *   -  ?         Enabled by default, but doesn't work with path to PATH_INFO rewrites
 *
 * All three of these punctuation problems can be avoided by using an alias, instead of a
 * rewrite rule of either variety.
 *
 * The problem with % is that when using a path to query rewrite rule, URLs are
 * double-unescaped: once by Apache's path conversion code, and again by PHP. So
 * %253F, for example, becomes "?". Our code does not double-escape to compensate
 * for this, indeed double escaping would break if the double-escaped title was
 * passed in the query string rather than the path. This is a minor security issue
 * because articles can be created such that they are hard to view or edit.
 *
 * In some rare cases you may wish to remove + for compatibility with old links.
 *
 * Theoretically 0x80-0x9F of ISO 8859-1 should be disallowed, but
 * this breaks interlanguage links
 */
$wgLegalTitleChars = " %!\"$&'()*,\\-.\\/0-9:;=?@A-Z\\\\^_`a-z~\\x80-\\xFF+";

/**
 * The interwiki prefix of the current wiki, or false if it doesn't have one.
 */
$wgLocalInterwiki = false;

/**
 * Expiry time for cache of interwiki table
 */
$wgInterwikiExpiry = 86400;

/** Interwiki caching settings.
  $wgInterwikiCache specifies path to constant database file
  This cdb database is generated by dumpInterwiki from maintenance
  and has such key formats:
  dbname:key - a simple key (e.g. enwiki:meta)
  _sitename:key - site-scope key (e.g. wiktionary:meta)
  __global:key - global-scope key (e.g. __global:meta)
  __sites:dbname - site mapping (e.g. __sites:enwiki)
  Sites mapping just specifies site name, other keys provide
  "local url" data layout.
  $wgInterwikiScopes specify number of domains to check for messages:
  1 - Just wiki(db)-level
  2 - wiki and global levels
  3 - site levels
  $wgInterwikiFallbackSite - if unable to resolve from cache
 */
$wgInterwikiCache = false;
$wgInterwikiScopes = 3;
$wgInterwikiFallbackSite = 'wiki';

/**
 * If local interwikis are set up which allow redirects,
 * set this regexp to restrict URLs which will be displayed
 * as 'redirected from' links.
 *
 * It might look something like this:
 * $wgRedirectSources = '!^https?://[a-z-]+\.wikipedia\.org/!';
 *
 * Leave at false to avoid displaying any incoming redirect markers.
 * This does not affect intra-wiki redirects, which don't change
 * the URL.
 */
$wgRedirectSources = false;

/**
 * Set this to false to avoid forcing the first letter of links to capitals.
 * WARNING: may break links! This makes links COMPLETELY case-sensitive. Links
 * appearing with a capital at the beginning of a sentence will *not* go to the
 * same place as links in the middle of a sentence using a lowercase initial.
 */
$wgCapitalLinks = true;

/**
 * @since 1.16 - This can now be set per-namespace. Some special namespaces (such
 * as Special, see MWNamespace::$alwaysCapitalizedNamespaces for the full list) must be
 * true by default (and setting them has no effect), due to various things that
 * require them to be so. Also, since Talk namespaces need to directly mirror their
 * associated content namespaces, the values for those are ignored in favor of the
 * subject namespace's setting. Setting for NS_MEDIA is taken automatically from
 * NS_FILE.
 * EX: $wgCapitalLinkOverrides[ NS_FILE ] = false;
 */
$wgCapitalLinkOverrides = array();

/** Which namespaces should support subpages?
 * See Language.php for a list of namespaces.
 */
$wgNamespacesWithSubpages = array(
    NS_TALK => true,
    NS_USER => true,
    NS_USER_TALK => true,
    NS_PROJECT_TALK => true,
    NS_FILE_TALK => true,
    NS_MEDIAWIKI => true,
    NS_MEDIAWIKI_TALK => true,
    NS_TEMPLATE_TALK => true,
    NS_HELP_TALK => true,
    NS_CATEGORY_TALK => true
);

/**
 * Array of namespaces which can be deemed to contain valid "content", as far
 * as the site statistics are concerned. Useful if additional namespaces also
 * contain "content" which should be considered when generating a count of the
 * number of articles in the wiki.
 */
$wgContentNamespaces = array(NS_MAIN);

/**
 * Max number of redirects to follow when resolving redirects.
 * 1 means only the first redirect is followed (default behavior).
 * 0 or less means no redirects are followed.
 */
$wgMaxRedirects = 1;

/**
 * Array of invalid page redirect targets.
 * Attempting to create a redirect to any of the pages in this array
 * will make the redirect fail.
 * Userlogout is hard-coded, so it does not need to be listed here.
 * (bug 10569) Disallow Mypage and Mytalk as well.
 *
 * As of now, this only checks special pages. Redirects to pages in
 * other namespaces cannot be invalidated by this variable.
 */
$wgInvalidRedirectTargets = array('Filepath', 'Mypage', 'Mytalk');

/** @} */ # End of title and interwiki settings }

/* * ********************************************************************* *//**
 * @name   Parser settings
 * These settings configure the transformation from wikitext to HTML.
 * @{
 */
/**
 * Parser configuration. Associative array with the following members:
 *
 *  class             The class name
 *
 *  preprocessorClass The preprocessor class. Two classes are currently available:
 *                    Preprocessor_Hash, which uses plain PHP arrays for tempoarary
 *                    storage, and Preprocessor_DOM, which uses the DOM module for
 *                    temporary storage. Preprocessor_DOM generally uses less memory;
 *                    the speed of the two is roughly the same.
 *
 *                    If this parameter is not given, it uses Preprocessor_DOM if the
 *                    DOM module is available, otherwise it uses Preprocessor_Hash.
 *
 * The entire associative array will be passed through to the constructor as
 * the first parameter. Note that only Setup.php can use this variable --
 * the configuration will change at runtime via $wgParser member functions, so
 * the contents of this variable will be out-of-date. The variable can only be
 * changed during LocalSettings.php, in particular, it can't be changed during
 * an extension setup function.
 */
$wgParserConf = array(
    'class' => 'Parser',
        #'preprocessorClass' => 'Preprocessor_Hash',
);

/** Maximum indent level of toc. */
$wgMaxTocLevel = 999;

/**
 * A complexity limit on template expansion
 */
$wgMaxPPNodeCount = 1000000;

/**
 * Maximum recursion depth for templates within templates.
 * The current parser adds two levels to the PHP call stack for each template,
 * and xdebug limits the call stack to 100 by default. So this should hopefully
 * stop the parser before it hits the xdebug limit.
 */
$wgMaxTemplateDepth = 40;

/** @see $wgMaxTemplateDepth */
$wgMaxPPExpandDepth = 40;

/** The external URL protocols */
$wgUrlProtocols = array(
    'http://',
    'https://',
    'ftp://',
    'irc://',
    'ircs://', // @bug 28503
    'gopher://',
    'telnet://', // Well if we're going to support the above.. -ævar
    'nntp://', // @bug 3808 RFC 1738
    'worldwind://',
    'mailto:',
    'news:',
    'svn://',
    'git://',
    'mms://',
    '//', // for protocol-relative URLs
);

/**
 * If true, removes (substitutes) templates in "~~~~" signatures.
 */
$wgCleanSignatures = true;

/**  Whether to allow inline image pointing to other websites */
$wgAllowExternalImages = false;

/**
 * If the above is false, you can specify an exception here. Image URLs
 * that start with this string are then rendered, while all others are not.
 * You can use this to set up a trusted, simple repository of images.
 * You may also specify an array of strings to allow multiple sites
 *
 * Examples:
 * <code>
 * $wgAllowExternalImagesFrom = 'http://127.0.0.1/';
 * $wgAllowExternalImagesFrom = array( 'http://127.0.0.1/', 'http://example.com' );
 * </code>
 */
$wgAllowExternalImagesFrom = '';

/** If $wgAllowExternalImages is false, you can allow an on-wiki
 * whitelist of regular expression fragments to match the image URL
 * against. If the image matches one of the regular expression fragments,
 * The image will be displayed.
 *
 * Set this to true to enable the on-wiki whitelist (MediaWiki:External image whitelist)
 * Or false to disable it
 */
$wgEnableImageWhitelist = true;

/**
 * A different approach to the above: simply allow the <img> tag to be used.
 * This allows you to specify alt text and other attributes, copy-paste HTML to
 * your wiki more easily, etc.  However, allowing external images in any manner
 * will allow anyone with editing rights to snoop on your visitors' IP
 * addresses and so forth, if they wanted to, by inserting links to images on
 * sites they control.
 */
$wgAllowImageTag = false;

/**
 * $wgUseTidy: use tidy to make sure HTML output is sane.
 * Tidy is a free tool that fixes broken HTML.
 * See http://www.w3.org/People/Raggett/tidy/
 *
 * - $wgTidyBin should be set to the path of the binary and
 * - $wgTidyConf to the path of the configuration file.
 * - $wgTidyOpts can include any number of parameters.
 * - $wgTidyInternal controls the use of the PECL extension or the
 *   libtidy (PHP >= 5) extension to use an in-process tidy library instead
 *   of spawning a separate program.
 *   Normally you shouldn't need to override the setting except for
 *   debugging. To install, use 'pear install tidy' and add a line
 *   'extension=tidy.so' to php.ini.
 */
$wgUseTidy = false;
/** @see $wgUseTidy */
$wgAlwaysUseTidy = false;
/** @see $wgUseTidy */
$wgTidyBin = 'tidy';
/** @see $wgUseTidy */
$wgTidyConf = $IP . '/includes/tidy.conf';
/** @see $wgUseTidy */
$wgTidyOpts = '';
/** @see $wgUseTidy */
$wgTidyInternal = extension_loaded('tidy');

/**
 * Put tidy warnings in HTML comments
 * Only works for internal tidy.
 */
$wgDebugTidy = false;

/** Allow raw, unchecked HTML in <html>...</html> sections.
 * THIS IS VERY DANGEROUS on a publicly editable site, so USE wgGroupPermissions
 * TO RESTRICT EDITING to only those that you trust
 */
$wgRawHtml = false;

/**
 * Set a default target for external links, e.g. _blank to pop up a new window
 */
$wgExternalLinkTarget = false;

/**
 * If true, external URL links in wiki text will be given the
 * rel="nofollow" attribute as a hint to search engines that
 * they should not be followed for ranking purposes as they
 * are user-supplied and thus subject to spamming.
 */
$wgNoFollowLinks = true;

/**
 * Namespaces in which $wgNoFollowLinks doesn't apply.
 * See Language.php for a list of namespaces.
 */
$wgNoFollowNsExceptions = array();

/**
 * If this is set to an array of domains, external links to these domain names
 * (or any subdomains) will not be set to rel="nofollow" regardless of the
 * value of $wgNoFollowLinks.  For instance:
 *
 * $wgNoFollowDomainExceptions = array( 'en.wikipedia.org', 'wiktionary.org' );
 *
 * This would add rel="nofollow" to links to de.wikipedia.org, but not
 * en.wikipedia.org, wiktionary.org, en.wiktionary.org, us.en.wikipedia.org,
 * etc.
 */
$wgNoFollowDomainExceptions = array();

/**
 * Set the minimum permissions required to edit pages in each
 * namespace.  If you list more than one permission, a user must
 * have all of them to edit pages in that namespace.
 *
 * Note: NS_MEDIAWIKI is implicitly restricted to editinterface.
 */
$wgNamespaceProtection = array();
// CONFIG_REVISION: expansion

/**
 * Automatically add a usergroup to any user who matches certain conditions.
 * The format is
 *   array( '&' or '|' or '^' or '!', cond1, cond2, ... )
 * where cond1, cond2, ... are themselves conditions; *OR*
 *   APCOND_EMAILCONFIRMED, *OR*
 *   array( APCOND_EMAILCONFIRMED ), *OR*
 *   array( APCOND_EDITCOUNT, number of edits ), *OR*
 *   array( APCOND_AGE, seconds since registration ), *OR*
 *   array( APCOND_INGROUPS, group1, group2, ... ), *OR*
 *   array( APCOND_ISIP, ip ), *OR*
 *   array( APCOND_IPINRANGE, range ), *OR*
 *   array( APCOND_AGE_FROM_EDIT, seconds since first edit ), *OR*
 *   array( APCOND_BLOCKED ), *OR*
 *   array( APCOND_ISBOT ), *OR*
 *   similar constructs defined by extensions.
 *
 * If $wgEmailAuthentication is off, APCOND_EMAILCONFIRMED will be true for any
 * user who has provided an e-mail address.
 */
$wgAutopromote = array(
    'autoconfirmed' => array('&',
        array(APCOND_EDITCOUNT, &$wgAutoConfirmCount),
        array(APCOND_AGE, &$wgAutoConfirmAge),
    ),
);
// CONFIG_REVISION: expansion

/**
 * Script used to scan IPs for open proxies.
 */
$wgProxyScriptPath = "$IP/maintenance/proxy_check.php";
// CONFIG_REVISION: expansion

/**
 * Set to set an explicit domain on the login cookies eg, "justthis.domain.org"
 * or ".any.subdomain.net"
 */
$wgCookieDomain = '';
// CONFIG_REVISION: environment-specific

/**
 * Filename for debug logging. See http://www.mediawiki.org/wiki/How_to_debug
 * The debug log file should be not be publicly accessible if it is used, as it
 * may contain private data.
 */
$wgDebugLogFile = '';
// CONFIG_REVISION: environment-specific

/**
 * If set to true, uncaught exceptions will print a complete stack trace
 * to output. This should only be used for debugging, as it may reveal
 * private information in function parameters due to PHP's backtrace
 * formatting.
 */
$wgShowExceptionDetails = false;
// CONFIG_REVISION: environment-specific

/**
 * List of namespaces which are searched by default. Example:
 *
 * <code>
 * $wgNamespacesToBeSearchedDefault[NS_MAIN] = true;
 * $wgNamespacesToBeSearchedDefault[NS_PROJECT] = true;
 * </code>
 */
$wgNamespacesToBeSearchedDefault = array(
    NS_MAIN => true,
);
// CONFIG_REVISION: expansion

/**
 * Namespaces to be searched when user clicks the "Help" tab
 * on Special:Search
 *
 * Same format as $wgNamespacesToBeSearchedDefault
 */
$wgNamespacesToBeSearchedHelp = array(
    NS_PROJECT => true,
    NS_HELP => true,
);
// CONFIG_REVISION: expansion

/**
 * Which namespaces have special treatment where they should be preview-on-open
 * Internaly only Category: pages apply, but using this extensions (e.g. Semantic MediaWiki)
 * can specify namespaces of pages they have special treatment for
 */
$wgPreviewOnOpenNamespaces = array(
    NS_CATEGORY => true
);
// CONFIG_REVISION: expansion

/**
 * @cond file_level_code
 * Set $wgCommandLineMode if it's not set already, to avoid notices
 */
if (!isset($wgCommandLineMode)) {
    $wgCommandLineMode = false;
}
// CONFIG_REVISION: logic, move somewhere else

/**
 * Set this to a string to put the wiki into read-only mode. The text will be
 * used as an explanation to users.
 *
 * This prevents most write operations via the web interface. Cache updates may
 * still be possible. To prevent database writes completely, use the read_only
 * option in MySQL.
 */
$wgReadOnly = null;
// CONFIG_REVISION: environment specific


/**
 * Set this to specify an external URL containing details about the content license used on your wiki.
 * If $wgRightsPage is set then this setting is ignored.
 */
$wgRightsUrl = null;
// CONFIG_REVISION: language specific, set with messaging

/**
 * Global list of hooks.
 * Add a hook by doing:
 *     $wgHooks['event_name'][] = $function;
 * or:
 *     $wgHooks['event_name'][] = array($function, $data);
 * or:
 *     $wgHooks['event_name'][] = array($object, 'method');
 */
// Wikia change - begin - @author: wladek
//$wgHooks = array();
$wgHooks = &Hooks::getHandlersArray();
// CONFIG_REVISION: move to expansions or elsewhere
// Wikia change - end

/**
 * Default robot policy.  The default policy is to encourage indexing and fol-
 * lowing of links.  It may be overridden on a per-namespace and/or per-page
 * basis.
 */
$wgDefaultRobotPolicy = 'index,follow';
// CONFIG_REVISION: environment specific

/**
 * Settings for incoming cross-site AJAX requests:
 * Newer browsers support cross-site AJAX when the target resource allows requests
 * from the origin domain by the Access-Control-Allow-Origin header.
 * This is currently only used by the API (requests to api.php)
 * $wgCrossSiteAJAXdomains can be set using a wildcard syntax:
 *
 * '*' matches any number of characters
 * '?' matches any 1 character
 *
 * Example:
  $wgCrossSiteAJAXdomains = array(
  'www.mediawiki.org',
  '*.wikipedia.org',
  '*.wikimedia.org',
  '*.wiktionary.org',
  );
 *
 */
$wgCrossSiteAJAXdomains = [
    "internal-vstf.{$wgWikiaBaseDomain}", # PLATFORM-1719
];

// CONFIG_REVISION: move to expansions.

/**
 * Timeout for HTTP requests done internally
 *
 * Let's use different values when running a maintenance script (that includes Wikia Tasks)
 * and when serving HTTP request
 *
 * @see PLATFORM-2385
 */
$wgHTTPTimeout = defined('RUN_MAINTENANCE_IF_MAIN') ? 25 : 5; # Wikia change
// CONFIG_REVISION: move to expansions.

/**
 * Proxy to use for CURL requests.
 */
$wgHTTPProxy = false;
// CONFIG_REVISION: datacenter / environment specific

/**
 * Filesystem extensions directory. Defaults to $IP/../extensions.
 *
 * To compile extensions with HipHop, set $wgExtensionsDirectory correctly,
 * and use code like:
 *
 *    require( MWInit::extensionSetupPath( 'Extension/Extension.php' ) );
 *
 * to include the extension setup file from LocalSettings.php. It is not
 * necessary to set this variable unless you use MWInit::extensionSetupPath().
 * @global string $wgExtensionsDirectory
 */
$wgExtensionsDirectory = "$IP/extensions";
// CONFIG_REVISION: move to expansions