// @flow
import React from "react";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text } from "../../../components";

export default class Feedback extends React.PureComponent {
  static propTypes = {
    text: PropTypes.string.isRequired
  };

  render() {
    return (
      <Text size="xxSmall" style={styles.title}>
        {this.props.text}
      </Text>
    );
  }
}
