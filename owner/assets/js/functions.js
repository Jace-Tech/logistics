const generateID = (prefix = "OCP") => {
  const timestamp = Date.now()
  const randomNumber = Math.floor(Math.random() * (10 ** 5));
  return prefix + timestamp + randomNumber;
}

console.log(generateID())