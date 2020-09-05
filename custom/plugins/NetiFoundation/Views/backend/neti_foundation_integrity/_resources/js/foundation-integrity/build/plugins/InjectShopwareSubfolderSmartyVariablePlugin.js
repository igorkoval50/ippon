const HtmlWebpackPlugin = require("html-webpack-plugin");

class InjectShopwareSubfolderSmartyVariablePlugin {
    constructor(options) {
        const defaults = {
            variable: "shopRoot",
        };

        this.options = Object.assign({}, defaults, options);
    }

    apply(compiler) {
        if ("production" !== process.env.NODE_ENV) {
            return;
        }

        let me = this;

        if (!compiler.hooks) {
            compiler.plugin('compilation', (compilation) => {
                compilation.plugin('html-webpack-plugin-alter-asset-tags', (tagDefinitions, cb) => {
                    tagDefinitions = me.injectSmartyVariable(tagDefinitions);

                    cb(null, tagDefinitions);
                });
            });

            return;
        }

        compiler.hooks.compilation.tap("InjectShopwareSubfolderSmartyVariablePlugin", (compilation) => {
            let beforeGenerationHook;

            if (compilation.hooks.htmlWebpackPluginAlterAssetTags) {
                // HtmlWebpackPlugin 3
                const { hooks } = compilation;
                beforeGenerationHook = hooks.htmlWebpackPluginAlterAssetTags;
            } else if (4 === HtmlWebpackPlugin.version) {
                // HtmlWebpackPlugin >= 4
                const hooks = HtmlWebpackPlugin.getHooks(compilation);
                beforeGenerationHook = hooks.alterAssetTags;
            }

            beforeGenerationHook.tapAsync("InjectShopwareSubfolderSmartyVariablePlugin", (tagDefinitions, cb) => {
                tagDefinitions = me.injectSmartyVariable(tagDefinitions);

                cb(null, tagDefinitions);
            });
        });
    }

    injectSmartyVariable(tagDefinitions, tags = ["head", "body"]) {
        tags.forEach(tag => {
            tagDefinitions[tag] = tagDefinitions[tag].map(this.injectSmartyVariableIntoTagDefinition.bind(this));
        });

        return tagDefinitions;
    }

    injectSmartyVariableIntoTagDefinition(tagDefinition) {
        const smartyVariable = "{$" + this.options.variable + "}";

        ["src", "href"].forEach(attribute => {
            if (tagDefinition.attributes[attribute]) {
                if ("/" !== tagDefinition.attributes[attribute].substr(0, 1)) {
                    tagDefinition.attributes[attribute] = "/" + tagDefinition.attributes[attribute];
                }

                tagDefinition.attributes[attribute] =
                    smartyVariable + this.addRootSlash(tagDefinition.attributes[attribute]);
            }
        });

        return tagDefinition;
    }

    addRootSlash(string) {
        if ("/" === string.substr(0, 1)) {
            return string;
        }

        return "/" + string;
    }
}

module.exports = InjectShopwareSubfolderSmartyVariablePlugin;
