// @flow
import { StyleSheet } from "react-native";
import { Colors } from "../../theme";

export default StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background.primary
  },
  scroll: {
    flex: 1
  },
  customContainerStyle: {
    paddingBottom: 0
  },
  errorContainer: {
    marginBottom: 0
  },
  containerStyle: { marginTop: 0 }
});
