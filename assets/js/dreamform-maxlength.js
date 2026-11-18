// Character counter for DreamForm textareas with a limit. The `maxlength` attribute comes from
// GsMmh\WebPlugin\MaxLengthValidation::htmxAttr(); index.php loads this file on pages that have one.
(() => {
  const init = () => {
    document.querySelectorAll('form.dreamform textarea[maxlength]').forEach(textarea => {
      if (textarea.dataset.counter) return;
      textarea.dataset.counter = 'on';

      const counter = document.createElement('p');
      counter.className = 'dreamform-counter';
      counter.id = `${textarea.id}-counter`;
      counter.setAttribute('aria-live', 'polite');
      textarea.after(counter);

      const describedBy = textarea.getAttribute('aria-describedby');
      textarea.setAttribute(
        'aria-describedby',
        describedBy ? `${describedBy} ${counter.id}` : counter.id
      );

      const update = () => {
        // Count like `maxlength` does (UTF-16 units, an emoji counts twice), so the counter
        // reaches the limit exactly when the browser stops input. The server counts characters
        // (mb_strlen) and is therefore never stricter.
        counter.textContent = `${textarea.value.length} / ${textarea.maxLength}`;
      };
      textarea.addEventListener('input', update);
      update();
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
