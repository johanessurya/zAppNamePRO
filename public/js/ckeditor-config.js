// Configure CKEditor Toolbar
CKEDITOR.editorConfig = function(config) {
  config.toolbar = [
    { name: 'basicstyles', items: ['Bold', 'Italic', 'Strike'] },
    { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent'] },
    { name: 'links', items: ['Link', 'Unlink'] }
  ];
};
