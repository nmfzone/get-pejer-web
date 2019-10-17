const mix = require('laravel-mix');
const path = require('path');
const root = path.resolve(__dirname);

/*
 |--------------------------------------------------------------------------
 | Extended Mix Configuration
 |--------------------------------------------------------------------------
 |
 | Here we define our custom Configuration.
 |
 */

const webpackConfig = {
  resolve: {
    symlinks: false,
    alias: {
      '@root': `${root}/resources/js`,
      '@common': `${root}/resources/js/common`,
      '@constants': `${root}/resources/js/constants`,
      '@views': `${root}/resources/js/views`,
      '@components': `${root}/resources/js/components`,
      '@store': `${root}/resources/js/store`,
      '@module': `${root}/resources/js/modules`,
      '@vendor': `${root}/vendor`
    }
  }
};

mix.webpackConfig(webpackConfig);

module.exports = webpackConfig;
