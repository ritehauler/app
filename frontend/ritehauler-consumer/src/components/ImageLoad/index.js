// @flow
import React from "react";
import PropTypes from "prop-types";
import ImageLoad from "react-native-image-placeholder";

export default class ImageLoadServer extends React.PureComponent {
  static propTypes = {
    // placeholderSource
    source: PropTypes.oneOfType([PropTypes.object, PropTypes.number]).isRequired
  };

  static defaultProps = {};

  state = {
    sourceImage: this.props.source
  };

  setImageSource = sourceImage => {
    this.setState({ sourceImage });
  };

  render() {
    const { source, ...rest } = this.props;
    const { sourceImage } = this.state;

    return <ImageLoad {...rest} source={sourceImage} />;
  }
}
