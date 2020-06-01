// @flow
import React from "react";
import { View } from "react-native";

import styles from "./styles";
import { Text } from "../../../components";
import { Strings } from "../../../theme";

export default class EmptyList extends React.PureComponent {
  render() {
    return (
      <View style={styles.container}>
        <Text>{Strings.noComplimentsFound}</Text>
      </View>
    );
  }
}
