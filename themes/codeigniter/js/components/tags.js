import Tagify from '@yaireo/tagify'

export function initTags(elem) {
  const tags = new Tagify (elem, {
    delimiters: ' |,|;',
    trim: false,
    maxTags: 5,
    pattern: /^[a-z0-9-]{0,20}$/,
    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
  });
}
