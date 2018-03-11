const { registerBlockType } = wp.blocks;
const el = wp.element.createElement;
const __ = wp.i18n.__;

registerBlockType( 'dainemawer/github' , {
    title: __( 'Github Embed' ),
    icon: 'id',
    category: 'widgets',
    attributes: {
        url: {
            type: 'string'
        }
    },
    edit: ( props ) => {
        
    },
    save: ( props ) => {

    }
});