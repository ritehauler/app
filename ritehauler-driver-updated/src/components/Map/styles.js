import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  containerStyle: {
    ...StyleSheet.absoluteFillObject,
    flex: 1,
    backgroundColor: Colors.background.login
  },
  mapStyle: {
    ...StyleSheet.absoluteFillObject
  }
});
