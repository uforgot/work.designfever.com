const webpack = require('webpack');
const path = require('path');

module.exports = {
    entry: {

        // common
        "df.workgroup.login.common": [
            './df.workgroup.Preset.js',
            './df.workgroup.GlobalVars.js',
            './df.workgroup.Util.js',
            './df.workgroup.login.LoadInfoData.js'
        ],
        "df.workgroup.login.custom": ['./df.workgroup.login.js']
    },
    output: {
        path: path.resolve(__dirname, './'),
        filename: '[name].bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                include: [
                    path.resolve(__dirname, './login')
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
    optimization: {
        minimize: true,
        //splitChunks: {}
    },
    resolve: {
        modules: ['node_modules'],
        extensions: ['.js']
    }
};