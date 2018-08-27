const webpack = require('webpack');
module.exports = {
    mode: 'development',
    entry: {
        app: ['df.workgroup.'],
    },
    output: {
        path: '',
        filename: '',
        publicPath: '',
    },
    module: {

    },
    plugins: [],
    optimization: {},
    resolve: {
        modules: ['node_modules'],
        extensions: ['.js'],
    },
};