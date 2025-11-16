<template>
    <Editor
    api-key="fff6ad7af947fbaaeef664e5416b0089f3ed631888550d2d4c4bf2f5ddc81a7f"
    tinymce-script-src="/node_modules/tinymce/tinymce.min.js"
    v-model="content"
    :init="{
    plugins: 'lists link image table code help wordcount',
    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
    }"
    />
    </template>

    <script>
    export default {
    data() {
    return {
    content: '',
    };
    },
    props: {
    modelValue: String,
    },
    watch: {
    modelValue: {
    immediate: true,
    handler(value) {
    this.content = value;
    },
    },
    content(value) {
    this.$emit('update:modelValue', value);
    },
    },
    };
    </script>
