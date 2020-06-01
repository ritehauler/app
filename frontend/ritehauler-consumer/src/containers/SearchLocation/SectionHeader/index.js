// @flow
import React from "react";
import PropTypes from "prop-types";

import styles from "./styles";
import { Text } from "../../../components";

export default class SectionHeader extends React.PureComponent {
  static propTypes = {
    title: PropTypes.string.isRequired
  };

  render() {
    const { title } = this.props;
    return (
      <Text size="xSmall" style={styles.sectionTitle}>
        {title}
      </Text>
    );
  }
}
