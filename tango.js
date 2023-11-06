function confirmDelete() {
  let result = confirm("Are you sure you want to delete this recipe?");
  if (result) {
    return true;
  } else {
    return false;
  }
}
