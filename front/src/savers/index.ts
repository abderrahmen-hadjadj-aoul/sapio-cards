import Saver from "./saver";
import web from "./web";
//import files from "./files";

const types = { web };

export default function(type, config = null): Saver {
  const class_ = types[type];
  if (!class_) {
    throw new Error(`Type ${type} does not exists`);
  }

  return new class_(config);
}
