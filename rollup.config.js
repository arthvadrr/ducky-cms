import postcss from 'rollup-plugin-postcss';
import postcssImport from 'postcss-import';

export default [
  // Setup bundle
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