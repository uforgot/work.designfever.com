const webpack = require('webpack');
const path = require('path');
var EncodingPlugin = require('webpack-encoding-plugin');

module.exports = {
    entry: {

        // common
        "df.workgroup.login.common": [
            '../common/df.workgroup.Preset.js',
            '../common/df.workgroup.GlobalVars.js',
            '../common/df.workgroup.Util.js',
            '../common/df.workgroup.login.LoadInfoData.js'
        ],
        "df.workgroup.login": ['../login/df.workgroup.login.js']
    },
    output: {
        path: path.resolve(__dirname, '../'),
        filename: '[name].bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.resolve(__dirname, '../login')
                ],
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                    }
                }
            }
        ]
    },
    plugins: [new EncodingPlugin({
        encoding: 'EUC-KR'
    })],
    optimization: {
        minimize: true,
        //splitChunks: {}
    },
    resolve: {
        modules: ['node_modules'],
        extensions: ['.js']
    }
};