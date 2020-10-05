// @flow
import React from "react";
import PropTypes from "prop-types";
import ImageLoad from "react-native-image-placeholder";

export default class ImageView extends React.PureComponent {
  static propTypes = {
    // placeholderSource
    source: PropTypes.oneOfType([PropTypes.object, PropTypes.number]).isRequired
  };

  render() {
    const { ...rest } = this.props;

    return <ImageLoad {...rest} />;
  }
}
