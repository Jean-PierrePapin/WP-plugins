wp.blocks.registerBlockType("ourplugin/are-you-paying-attention", {
    title: "Are You Paying Attention?",
    icon: "smiley",
    category: "common",
    edit: function () {
        return <h3>This is from JSX</h3>
    },
    save: function () {
        return wp.element.createElement("h1", null, "This is the frontend.");
    }
})