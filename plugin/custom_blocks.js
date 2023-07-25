(function(blocks, editor, element) {
  var el = element.createElement;
  var __ = wp.i18n.__;
  var RichText = editor.RichText;
  var blockStyle = {
    backgroundColor: '#f3f3f3',
    padding: '20px',
    marginBottom: '20px',
  };

  blocks.registerBlockType('your-plugin/bilibili-block', {
    title: __('Bilibili Block', 'your-plugin'),
    icon: 'video-alt2',
    category: 'common',

    attributes: {
      content: {
        type: 'array',
        source: 'children',
        selector: 'p',
      },
    },

    edit: function(props) {
      var content = props.attributes.content;
      var onChangeContent = function(newContent) {
        props.setAttributes({ content: newContent });
      };

      return el(
        'div',
        { style: blockStyle },
        el(RichText, {
          tagName: 'p',
          value: content,
          onChange: onChangeContent,
          placeholder: __('请输入B站视频VID...', 'your-plugin'),
          style: {
              color:'#00a1d6',
              border: '2px solid',
              padding: '5px 15px',
              display: 'inline',
            //   'border-radius': '8px'
          }
        })
      );
    },

    save: function(props) {
    // var vid = props.attributes.vid;
    // // 调用 do_shortcode 函数执行 bilibili_embed 简码
    // const content = element.do_shortcode(`[bilibili_embed vid=${vid}]`);
    
    // return element.createElement('div', {
    // dangerouslySetInnerHTML: { __html: content },
    // });
      var content = props.attributes.content;
      return el('div', { style: blockStyle }, el(RichText.Content, {
        tagName: 'p',
        value: content,
      }));
    },
  });
})(
  window.wp.blocks,
  window.wp.editor,
  window.wp.element
);
