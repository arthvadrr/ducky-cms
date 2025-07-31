import postcss from 'rollup-plugin-postcss';
import postcssImport from 'postcss-import';

export default {
  input: 'styles/index.css',
  output: {
    name: 'noop',
    file: 'dist/build.css',
    format: 'iife',
  },
  plugins: [
    postcss({
      plugins: [postcssImport()],
      extract: true,
      minimize: true,
    }),
  ],
};