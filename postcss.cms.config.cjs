module.exports = {
    plugins: [
        require('postcss-prefix-selector')({
            prefix: '#cms-page-content',

            transform(prefix, selector, prefixedSelector) {
                if (selector === ':root' || selector === 'body') {
                    return prefix;
                }

                if (selector.startsWith('body ')) {
                    return prefix + selector.substring(4);
                }

                return prefixedSelector;
            }
        })
    ]
};
