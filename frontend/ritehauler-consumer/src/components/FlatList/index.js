// @flow
import React from "react";
import PropTypes from "prop-types";
import { FlatList as FlatListRN } from "react-native";

import Util from "../../util";
import { Separator } from "../";
import styles from "./styles";

export default class FlatList extends React.Component {
  static propTypes = {
    data: PropTypes.array.isRequired,
    renderItem: PropTypes.func.isRequired,
    keyExtractor: PropTypes.func,
    ItemSeparatorComponent: PropTypes.func
  };

  static defaultProps = {
    keyExtractor: Util.keyExtractor,
    ItemSeparatorComponent: () => <Separator />
  };

  render() {
    const { ...rest } = this.props;
    return <FlatListRN style={styles.container} {...rest} />;
  }
}
