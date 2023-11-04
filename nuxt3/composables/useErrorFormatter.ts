export default (error: any) => {
  let message = error.message;

  switch (error.statusCode) {
    case 500:
      message = "Error processing the request";
      break;
    case 404:
      message = "Page not found";
      break;
  }
  error.message = message;
  return error;
};
