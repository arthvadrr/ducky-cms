import postcss from 'rollup-plugin-postcss';
import postcssImport from 'postcss-import';

// Export multiple build configs so Rollup watches/builds both bundles
export default [
  // Setup bundle (keeps existing behavior/output name)
  {
    input: 'styles/setup.css',
    output: {
      name: 'noop',
      file: 'dist/setup.bundle.js',
      format: 'iife',
    },
    plugins: [
      postcss({
        plugins: [postcssImport()],
        // Extract setup bundle CSS to desired filename
        extract: 'setup.css',
        minimize: true,
      }),
    ],
  },
  // Admin bundle
  {
    input: 'styles/admin.css',
    output: {
      name: 'noop',
      file: 'dist/admin.bundle.js',
      format: 'iife',
    },
    plugins: [
      postcss({
        plugins: [postcssImport()],
        extract: 'admin.css',
        minimize: true,
      }),
    ],
  },
];